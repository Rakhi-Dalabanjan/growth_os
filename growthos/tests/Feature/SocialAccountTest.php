<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SocialAccountTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Guest cannot access social accounts routes.
     */
    public function test_guests_cannot_access_social_accounts(): void
    {
        $response = $this->get('/social-accounts');
        $response->assertRedirect('/login');

        $response = $this->get('/social-accounts/connect');
        $response->assertRedirect('/login');
    }

    /**
     * User without organization is redirected to setup organization.
     */
    public function test_user_without_org_redirected_to_org_create(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/social-accounts');
        $response->assertRedirect('/organization/create');
    }

    /**
     * Authenticated user with organization can access social accounts index.
     */
    public function test_authenticated_user_can_access_social_accounts_index(): void
    {
        $org = Organization::create(['name' => 'Acme Corp']);
        $user = User::factory()->create(['organization_id' => $org->id]);

        $response = $this->actingAs($user)->get('/social-accounts');
        $response->assertStatus(200);
        $response->assertSee('Social Account Connections');
    }

    /**
     * State validation protection during callback.
     */
    public function test_callback_fails_with_invalid_state(): void
    {
        $org = Organization::create(['name' => 'Acme Corp']);
        $user = User::factory()->create(['organization_id' => $org->id]);

        session(['oauth_state' => 'expected_state']);

        $response = $this->actingAs($user)
            ->get('/social-accounts/callback?code=mock_code&state=invalid_state');

        $response->assertRedirect('/social-accounts');
        $response->assertSessionHas('error', 'Invalid OAuth state validation failed. Potential CSRF attack.');
    }

    /**
     * Callback stores account correctly in simulator/mock mode.
     */
    public function test_callback_stores_selected_accounts_in_mock_mode(): void
    {
        $org = Organization::create(['name' => 'Acme Corp']);
        $user = User::factory()->create(['organization_id' => $org->id]);

        session(['oauth_state' => 'valid_state']);

        $response = $this->actingAs($user)
            ->get('/social-accounts/callback?code=mock_code&state=valid_state&pages=1001,1002&instagrams=5001');

        $response->assertRedirect('/social-accounts');
        $response->assertSessionHas('success');

        // Check Facebook Page 1001 connected
        $this->assertDatabaseHas('social_accounts', [
            'organization_id' => $org->id,
            'platform' => 'facebook',
            'page_id' => '1001',
            'status' => 'connected',
        ]);

        // Check Facebook Page 1002 connected
        $this->assertDatabaseHas('social_accounts', [
            'organization_id' => $org->id,
            'platform' => 'facebook',
            'page_id' => '1002',
            'status' => 'connected',
        ]);

        // Check Instagram connected
        $this->assertDatabaseHas('social_accounts', [
            'organization_id' => $org->id,
            'platform' => 'instagram',
            'instagram_business_id' => '5001',
            'status' => 'connected',
        ]);

        // Verify tokens are encrypted in the database but decrypted upon retrieving
        $account = SocialAccount::where('organization_id', $org->id)
            ->where('platform', 'facebook')
            ->where('page_id', '1001')
            ->first();

        $this->assertEquals('mock_page_token_acme_1001', $account->access_token);
        
        // Assert raw database value is encrypted
        $rawRow = DB::table('social_accounts')->where('id', $account->id)->first();
        $this->assertNotEquals('mock_page_token_acme_1001', $rawRow->access_token);
    }

    /**
     * Duplicate connections are updated rather than creating duplicate records.
     */
    public function test_reconnecting_updates_existing_record_to_prevent_duplicates(): void
    {
        $org = Organization::create(['name' => 'Acme Corp']);
        $user = User::factory()->create(['organization_id' => $org->id]);

        // Create an existing disconnected account
        $account = SocialAccount::create([
            'organization_id' => $org->id,
            'platform' => 'facebook',
            'page_id' => '1001',
            'page_name' => 'Old Page Name',
            'account_name' => 'Old Page Name',
            'access_token' => null,
            'status' => 'disconnected',
        ]);

        session(['oauth_state' => 'valid_state']);

        $response = $this->actingAs($user)
            ->get('/social-accounts/callback?code=mock_code&state=valid_state&pages=1001');

        $response->assertRedirect('/social-accounts');

        // Verify no duplicate record was created
        $this->assertEquals(1, SocialAccount::where('organization_id', $org->id)
            ->where('platform', 'facebook')
            ->where('page_id', '1001')
            ->count());

        $account->refresh();
        $this->assertEquals('connected', $account->status);
        $this->assertEquals('Acme Corp Facebook Page', $account->page_name);
        $this->assertEquals('mock_page_token_acme_1001', $account->access_token);
    }

    /**
     * Tenant Isolation: User cannot manage another organization's social accounts.
     */
    public function test_tenant_isolation_cannot_disconnect_other_organizations_account(): void
    {
        $org1 = Organization::create(['name' => 'Org One']);
        $user1 = User::factory()->create(['organization_id' => $org1->id]);

        $org2 = Organization::create(['name' => 'Org Two']);
        $account2 = SocialAccount::create([
            'organization_id' => $org2->id,
            'platform' => 'facebook',
            'page_id' => '2001',
            'page_name' => 'Other Org Page',
            'access_token' => 'secret_token',
            'status' => 'connected',
        ]);

        $response = $this->actingAs($user1)
            ->post("/social-accounts/{$account2->id}/disconnect");

        $response->assertStatus(403); // Forbidden
        
        $account2->refresh();
        $this->assertEquals('connected', $account2->status);
        $this->assertEquals('secret_token', $account2->access_token);
    }

    /**
     * Disconnect action works: Removes token values and updates status to disconnected.
     */
    public function test_disconnect_clears_tokens_and_keeps_metadata(): void
    {
        $org = Organization::create(['name' => 'Acme Corp']);
        $user = User::factory()->create(['organization_id' => $org->id]);

        $account = SocialAccount::create([
            'organization_id' => $org->id,
            'platform' => 'facebook',
            'page_id' => '1001',
            'page_name' => 'Acme Facebook',
            'account_name' => 'Acme Facebook',
            'access_token' => 'active_token',
            'status' => 'connected',
        ]);

        $response = $this->actingAs($user)
            ->post("/social-accounts/{$account->id}/disconnect");

        $response->assertRedirect('/social-accounts');
        $response->assertSessionHas('success');

        $account->refresh();
        $this->assertEquals('disconnected', $account->status);
        $this->assertNull($account->access_token);
        $this->assertNull($account->refresh_token);
        $this->assertEquals('1001', $account->page_id); // Metadata history remains intact
    }
}
