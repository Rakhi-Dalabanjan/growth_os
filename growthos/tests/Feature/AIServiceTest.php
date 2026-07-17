<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AIServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Unauthenticated guest users must be redirected.
     */
    public function test_guest_cannot_access_ai_service(): void
    {
        $this->get('/ai-service')->assertRedirect('/login');
        $this->post('/ai-service/ping')->assertRedirect('/login');
        $this->post('/ai-service/health')->assertRedirect('/login');
        $this->post('/ai-service/echo')->assertRedirect('/login');
    }

    /**
     * Authenticated users can view the connection panel index.
     */
    public function test_authenticated_user_can_access_ai_service_index(): void
    {
        $user = User::factory()->create();

        Http::fake([
            '*/health' => Http::response([
                'success' => true,
                'message' => 'Health Check',
                'data' => [
                    'status' => 'online',
                    'version' => '1.0.0',
                    'uptime' => 3600.5
                ]
            ], 200),
        ]);

        $response = $this->actingAs($user)->get('/ai-service');

        $response->assertStatus(200);
        $response->assertSee('AI Service Integration');
        $response->assertSee('online');
        $response->assertSee('1.0.0');
    }

    /**
     * Test AJAX ping endpoint success.
     */
    public function test_ajax_ping_success(): void
    {
        $user = User::factory()->create();

        Http::fake([
            '*/ping' => Http::response([
                'success' => true,
                'message' => 'Pong',
                'data' => ['ping' => 'pong', 'received' => []]
            ], 200),
        ]);

        $response = $this->actingAs($user)->postJson('/ai-service/ping');

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('status', 200);
        $response->assertJsonPath('data.message', 'Pong');

        // Assert request included token header
        Http::assertSent(function ($request) {
            return $request->hasHeader('X-API-Token', 'growthos_ai_secret_token') &&
                   $request->url() === 'http://127.0.0.1:8080/ping';
        });
    }

    /**
     * Test AJAX health check endpoint success.
     */
    public function test_ajax_health_success(): void
    {
        $user = User::factory()->create();

        Http::fake([
            '*/health' => Http::response([
                'success' => true,
                'message' => 'Health Check',
                'data' => [
                    'status' => 'online',
                    'version' => '1.0.0',
                    'uptime' => 120.5
                ]
            ], 200),
        ]);

        $response = $this->actingAs($user)->postJson('/ai-service/health');

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('status', 200);
        $response->assertJsonPath('data.data.status', 'online');
        $response->assertJsonPath('data.data.version', '1.0.0');
    }

    /**
     * Test AJAX echo endpoint success.
     */
    public function test_ajax_echo_success(): void
    {
        $user = User::factory()->create();
        $payload = ['msg' => 'Test Echo Payload'];

        Http::fake([
            '*/echo' => Http::response([
                'success' => true,
                'message' => 'Echo',
                'data' => $payload
            ], 200),
        ]);

        $response = $this->actingAs($user)->postJson('/ai-service/echo', [
            'payload' => $payload
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('data.data', $payload);
    }

    /**
     * Test AI service offline handling.
     */
    public function test_ai_service_offline_handling(): void
    {
        $user = User::factory()->create();

        Http::fake([
            '*/ping' => function () {
                throw new \Illuminate\Http\Client\ConnectionException("Connection refused");
            }
        ]);

        $response = $this->actingAs($user)->postJson('/ai-service/ping');

        $response->assertStatus(200); // Controller returns 200 but response indicates failure
        $response->assertJsonPath('success', false);
        $response->assertJsonPath('status', 504);
        $response->assertJsonFragment(['error' => 'Connection to AI Service failed or timed out: Connection refused']);
    }
}
