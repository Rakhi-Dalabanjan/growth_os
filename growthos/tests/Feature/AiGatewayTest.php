<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AiGatewayTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Unauthenticated guest users must be redirected.
     */
    public function test_guest_cannot_access_ai_gateway(): void
    {
        $this->get('/ai-gateway')->assertRedirect('/login');
        $this->post('/ai-gateway/test')->assertRedirect('/login');
        $this->post('/ai-gateway/health')->assertRedirect('/login');
        $this->post('/ai-gateway/providers')->assertRedirect('/login');
    }

    /**
     * Authenticated users can view the AI Gateway control panel.
     */
    public function test_authenticated_user_can_access_ai_gateway_index(): void
    {
        $user = User::factory()->create();

        Http::fake([
            '*/ai/providers' => Http::response([
                'success' => true,
                'message' => 'Available AI providers retrieved successfully.',
                'provider' => 'gateway',
                'execution_time' => 0.005,
                'data' => [
                    'active_provider' => 'gemini',
                    'providers' => [
                        ['name' => 'gemini', 'installed' => true, 'status' => 'active', 'mode' => 'mock'],
                        ['name' => 'openai', 'installed' => true, 'status' => 'not_implemented', 'mode' => 'unimplemented'],
                        ['name' => 'claude', 'installed' => true, 'status' => 'not_implemented', 'mode' => 'unimplemented']
                    ]
                ]
            ], 200),
            '*/ai/health' => Http::response([
                'success' => true,
                'message' => 'AI Gateway health query successful.',
                'provider' => 'gemini',
                'execution_time' => 0.02,
                'data' => [
                    'gateway_status' => 'online',
                    'provider_health' => 'online',
                    'latency_ms' => 20.0,
                    'version' => '1.0.0'
                ]
            ], 200),
        ]);

        $response = $this->actingAs($user)->get('/ai-gateway');

        $response->assertStatus(200);
        $response->assertSee('AI Gateway Control Panel');
        $response->assertSee('Gemini');
        $response->assertSee('online');
    }

    /**
     * Test prompt execution endpoint.
     */
    public function test_ajax_prompt_execution_success(): void
    {
        $user = User::factory()->create();

        Http::fake([
            '*/ai/test' => Http::response([
                'success' => true,
                'message' => 'Test prompt generated successfully.',
                'provider' => 'gemini',
                'execution_time' => 0.12,
                'data' => [
                    'text' => 'This is a simulated AI response content.',
                    'model' => 'gemini-2.0-flash'
                ]
            ], 200),
        ]);

        $response = $this->actingAs($user)->postJson('/ai-gateway/test', [
            'prompt' => 'Hello AI'
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('provider', 'gemini');
        $response->assertJsonPath('data.text', 'This is a simulated AI response content.');
        
        Http::assertSent(function ($request) {
            return $request->hasHeader('X-API-Token', 'growthos_ai_secret_token') &&
                   $request->url() === 'http://127.0.0.1:8080/ai/test' &&
                   $request['prompt'] === 'Hello AI';
        });
    }

    /**
     * Test prompt execution missing parameter.
     */
    public function test_ajax_prompt_execution_validation_error(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/ai-gateway/test', [
            'prompt' => ''
        ]);

        $response->assertStatus(422); // Validation fails
    }

    /**
     * Test gateway offline exception handling.
     */
    public function test_gateway_offline_exception_handling(): void
    {
        $user = User::factory()->create();

        Http::fake([
            '*/ai/test' => function () {
                throw new \Illuminate\Http\Client\ConnectionException("Network unreachable");
            }
        ]);

        $response = $this->actingAs($user)->postJson('/ai-gateway/test', [
            'prompt' => 'Test prompt'
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', false);
        $response->assertJsonPath('status', 504);
        $response->assertJsonFragment(['error' => 'Connection to AI Service failed or timed out: Network unreachable']);
    }
}
