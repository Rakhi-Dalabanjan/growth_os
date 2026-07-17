<?php

namespace Tests\Feature;

use App\Models\BrandProfile;
use App\Models\Organization;
use App\Models\User;
use App\Services\BrandProfileCompletionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BrandProfileTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Guest cannot access brand profile routes.
     */
    public function test_guests_cannot_access_brand_profile(): void
    {
        $response = $this->get('/brand-profile');
        $response->assertRedirect('/login');
    }

    /**
     * User without organization is redirected to setup organization.
     */
    public function test_user_without_org_redirected_to_org_create(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/brand-profile');
        $response->assertRedirect('/organization/create');
    }

    /**
     * User with organization can access create page if no profile exists.
     */
    public function test_user_can_access_brand_profile_create_page(): void
    {
        $org = Organization::create(['name' => 'Acme Corp']);
        $user = User::factory()->create(['organization_id' => $org->id]);

        $response = $this->actingAs($user)->get('/brand-profile/create');
        $response->assertStatus(200);
    }

    /**
     * User is redirected to show page if profile already exists.
     */
    public function test_user_redirected_if_profile_already_exists(): void
    {
        $org = Organization::create(['name' => 'Acme Corp']);
        $user = User::factory()->create(['organization_id' => $org->id]);
        $profile = BrandProfile::create([
            'organization_id' => $org->id,
            'brand_name' => 'Acme',
        ]);

        $response = $this->actingAs($user)->get('/brand-profile/create');
        $response->assertRedirect("/brand-profile/{$profile->id}");
    }

    /**
     * User can store a brand profile.
     */
    public function test_user_can_create_brand_profile(): void
    {
        $org = Organization::create(['name' => 'Acme Corp']);
        $user = User::factory()->create(['organization_id' => $org->id]);

        $response = $this->actingAs($user)->post('/brand-profile', [
            'brand_name'           => 'Acme',
            'tagline'              => 'Innovating solutions',
            'business_description' => 'A tech company',
            'primary_color'        => '#112233',
            'preferred_words'      => 'easy, fast, solid',
            'competitor_names'     => 'Comp A, Comp B',
        ]);

        $this->assertDatabaseHas('brand_profiles', [
            'brand_name'      => 'Acme',
            'organization_id' => $org->id,
        ]);

        $profile = BrandProfile::where('organization_id', $org->id)->first();
        $response->assertRedirect("/brand-profile/{$profile->id}");

        // Assert JSON fields casted to arrays properly
        $this->assertEquals(['easy', 'fast', 'solid'], $profile->preferred_words);
        $this->assertEquals(['Comp A', 'Comp B'], $profile->competitor_names);
    }

    /**
     * User can view their brand profile.
     */
    public function test_user_can_view_own_brand_profile(): void
    {
        $org = Organization::create(['name' => 'Acme Corp']);
        $user = User::factory()->create(['organization_id' => $org->id]);
        $profile = BrandProfile::create([
            'organization_id' => $org->id,
            'brand_name' => 'Acme',
        ]);

        $response = $this->actingAs($user)->get("/brand-profile/{$profile->id}");
        $response->assertStatus(200);
        $response->assertSee('Acme');
    }

    /**
     * Tenant Isolation: User cannot view another organization's brand profile.
     */
    public function test_tenant_isolation_cannot_view_other_brand_profile(): void
    {
        $org1 = Organization::create(['name' => 'Org One']);
        $user1 = User::factory()->create(['organization_id' => $org1->id]);

        $org2 = Organization::create(['name' => 'Org Two']);
        $profile2 = BrandProfile::create([
            'organization_id' => $org2->id,
            'brand_name' => 'Brand Two',
        ]);

        $response = $this->actingAs($user1)->get("/brand-profile/{$profile2->id}");
        $response->assertStatus(403); // Forbidden
    }

    /**
     * User can edit and update their brand profile.
     */
    public function test_user_can_edit_and_update_brand_profile(): void
    {
        $org = Organization::create(['name' => 'Acme Corp']);
        $user = User::factory()->create(['organization_id' => $org->id]);
        $profile = BrandProfile::create([
            'organization_id' => $org->id,
            'brand_name' => 'Old Name',
        ]);

        $response = $this->actingAs($user)->get("/brand-profile/{$profile->id}/edit");
        $response->assertStatus(200);

        $response = $this->actingAs($user)->put("/brand-profile/{$profile->id}", [
            'brand_name'      => 'New Name',
            'preferred_words' => 'updated, words',
        ]);

        $response->assertRedirect("/brand-profile/{$profile->id}");
        $profile->refresh();
        $this->assertEquals('New Name', $profile->brand_name);
        $this->assertEquals(['updated', 'words'], $profile->preferred_words);
    }

    /**
     * Tenant Isolation: User cannot update another organization's brand profile.
     */
    public function test_tenant_isolation_cannot_update_other_brand_profile(): void
    {
        $org1 = Organization::create(['name' => 'Org One']);
        $user1 = User::factory()->create(['organization_id' => $org1->id]);

        $org2 = Organization::create(['name' => 'Org Two']);
        $profile2 = BrandProfile::create([
            'organization_id' => $org2->id,
            'brand_name' => 'Brand Two',
        ]);

        $response = $this->actingAs($user1)->put("/brand-profile/{$profile2->id}", [
            'brand_name' => 'Hacked Brand',
        ]);

        $response->assertStatus(403);
        $profile2->refresh();
        $this->assertEquals('Brand Two', $profile2->brand_name);
    }

    /**
     * Verify completion score calculations.
     */
    public function test_completion_score_service(): void
    {
        $org = Organization::create(['name' => 'Acme Corp']);
        $profile = BrandProfile::create([
            'organization_id' => $org->id,
            'brand_name' => 'Acme', // 1 field filled
        ]);

        $service = new BrandProfileCompletionService();
        $completion = $service->calculate($profile);

        // 1 of 24 is ~4.16%, rounded to 4%
        $this->assertEquals(4, $completion['percentage']);
        $this->assertEquals('Basic', $completion['status']);
        $this->assertEquals('warning', $completion['color']);

        // Let's fill all 24 fields
        $profile->tagline = 'tag';
        $profile->business_description = 'desc';
        $profile->mission = 'mission';
        $profile->vision = 'vision';
        $profile->primary_market = 'market';
        $profile->target_audience = 'audience';
        $profile->brand_tone = 'tone';
        $profile->formality = 'formality';
        $profile->language = 'lang';
        $profile->emoji_style = 'emoji';
        $profile->primary_color = '#111';
        $profile->secondary_color = '#222';
        $profile->accent_color = '#333';
        $profile->primary_font = 'font1';
        $profile->secondary_font = 'font2';
        $profile->primary_cta = 'cta1';
        $profile->secondary_cta = 'cta2';
        $profile->preferred_words = ['one'];
        $profile->restricted_words = ['two'];
        $profile->competitor_names = ['three'];
        $profile->approved_claims = ['four'];
        $profile->restricted_claims = ['five'];
        $profile->legal_disclaimer = 'legal';
        $profile->save();

        $completion = $service->calculate($profile);
        $this->assertEquals(100, $completion['percentage']);
        $this->assertEquals('Complete', $completion['status']);
        $this->assertEquals('success', $completion['color']);
    }
}
