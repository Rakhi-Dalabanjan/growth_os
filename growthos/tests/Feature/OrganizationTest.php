<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrganizationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Unauthenticated users cannot access organization routes.
     */
    public function test_guests_cannot_access_organization(): void
    {
        $response = $this->get('/organization');
        $response->assertRedirect('/login');
    }

    /**
     * Authenticated users without an org are redirected to create.
     */
    public function test_user_without_org_is_redirected_to_create(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/organization');
        $response->assertRedirect('/organization/create');
    }

    /**
     * Create organization form is accessible.
     */
    public function test_create_organization_form_is_accessible(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/organization/create');
        $response->assertOk();
    }

    /**
     * User can create an organization.
     */
    public function test_user_can_create_organization(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post('/organization', [
                'name'    => 'Acme Corp',
                'country' => 'United States',
                'status'  => 'active',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('organizations', ['name' => 'Acme Corp']);

        $user->refresh();
        $this->assertNotNull($user->organization_id);
    }

    /**
     * User can view their own organization.
     */
    public function test_user_can_view_own_organization(): void
    {
        $org  = Organization::create(['name' => 'Test Org', 'status' => 'active']);
        $user = User::factory()->create(['organization_id' => $org->id]);

        $response = $this->actingAs($user)->get("/organization/{$org->id}");
        $response->assertOk();
    }

    /**
     * Tenant isolation: user cannot view another org's data.
     */
    public function test_user_cannot_view_another_organizations_data(): void
    {
        $org1  = Organization::create(['name' => 'Org One', 'status' => 'active']);
        $org2  = Organization::create(['name' => 'Org Two', 'status' => 'active']);
        $user  = User::factory()->create(['organization_id' => $org1->id]);

        $response = $this->actingAs($user)->get("/organization/{$org2->id}");
        $response->assertForbidden();
    }

    /**
     * User can update their own organization.
     */
    public function test_user_can_update_own_organization(): void
    {
        $org  = Organization::create(['name' => 'Old Name', 'status' => 'active']);
        $user = User::factory()->create(['organization_id' => $org->id]);

        $response = $this
            ->actingAs($user)
            ->put("/organization/{$org->id}", [
                'name'   => 'New Name',
                'status' => 'active',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('organizations', ['name' => 'New Name']);
    }

    /**
     * Tenant isolation: user cannot update another org.
     */
    public function test_user_cannot_update_another_organization(): void
    {
        $org1 = Organization::create(['name' => 'Org One', 'status' => 'active']);
        $org2 = Organization::create(['name' => 'Org Two', 'status' => 'active']);
        $user = User::factory()->create(['organization_id' => $org1->id]);

        $response = $this
            ->actingAs($user)
            ->put("/organization/{$org2->id}", [
                'name'   => 'Hacked Name',
                'status' => 'active',
            ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('organizations', ['name' => 'Hacked Name']);
    }
}
