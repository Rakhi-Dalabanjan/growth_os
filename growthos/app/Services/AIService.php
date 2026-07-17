<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\ConnectionException;
use Exception;

class AIService
{
    protected string $baseUrl;
    protected int $timeout;
    protected string $apiToken;

    public function __construct()
    {
        $this->baseUrl  = config('services.ai.url', 'http://127.0.0.1:8080');
        $this->timeout  = config('services.ai.timeout', 5);
        $this->apiToken = config('services.ai.api_token', 'growthos_ai_secret_token');
    }

    /**
     * Get live status of the service, optimized for the dashboard (short timeout).
     */
    public function getStatusForDashboard(): array
    {
        $start = microtime(true);
        try {
            $response = Http::withHeaders([
                'X-API-Token' => $this->apiToken,
            ])->timeout(1.5)
              ->retry(2, 50)
              ->get("{$this->baseUrl}/health");

            $duration = round((microtime(true) - $start) * 1000, 2);

            if ($response->successful()) {
                $body = $response->json();
                return [
                    'status' => 'online',
                    'version' => $body['data']['version'] ?? '1.0.0',
                    'uptime' => $body['data']['uptime'] ?? 0,
                    'latency' => $duration,
                    'error' => null,
                ];
            }

            return [
                'status' => 'error',
                'version' => null,
                'uptime' => 0,
                'latency' => $duration,
                'error' => "HTTP error: {$response->status()}",
            ];
        } catch (ConnectionException $e) {
            $duration = round((microtime(true) - $start) * 1000, 2);
            return [
                'status' => 'offline',
                'version' => null,
                'uptime' => 0,
                'latency' => $duration,
                'error' => 'Connection timed out or host unreachable',
            ];
        } catch (Exception $e) {
            $duration = round((microtime(true) - $start) * 1000, 2);
            return [
                'status' => 'offline',
                'version' => null,
                'uptime' => 0,
                'latency' => $duration,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Call Ping endpoint
     */
    public function ping(array $payload = []): array
    {
        return $this->request('POST', '/ping', $payload);
    }

    /**
     * Call Echo endpoint
     */
    public function echo(array $payload): array
    {
        return $this->request('POST', '/echo', $payload);
    }

    /**
     * Call AI Gateway Health endpoint
     */
    public function health(): array
    {
        return $this->request('GET', '/ai/health');
    }

    /**
     * Call AI Gateway Providers list endpoint
     */
    public function providers(): array
    {
        return $this->request('GET', '/ai/providers');
    }

    /**
     * Call AI Gateway Test Prompt endpoint
     */
    public function testPrompt(string $prompt): array
    {
        return $this->request('POST', '/ai/test', ['prompt' => $prompt]);
    }

    /**
     * Helper to perform HTTP requests with retry logic, latency measurement, and logging.
     */
    protected function request(string $method, string $endpoint, array $payload = []): array
    {
        $url = rtrim($this->baseUrl, '/') . '/' . ltrim($endpoint, '/');
        $start = microtime(true);

        Log::info("AI Service Request: {$method} {$endpoint}", [
            'payload' => $payload
        ]);

        try {
            $request = Http::withHeaders([
                'X-API-Token' => $this->apiToken,
            ])->timeout($this->timeout)->retry(3, 100);

            if (strtoupper($method) === 'POST') {
                $response = $request->post($url, $payload);
            } else {
                $response = $request->get($url);
            }

            $duration = round((microtime(true) - $start) * 1000, 2);

            Log::info("AI Service Response: {$method} {$endpoint} - Status: {$response->status()}", [
                'latency_ms' => $duration,
                'response' => $response->json()
            ]);

            return [
                'success' => $response->successful(),
                'status' => $response->status(),
                'data' => $response->json(),
                'latency' => $duration,
                'error' => null,
            ];
        } catch (ConnectionException $e) {
            $duration = round((microtime(true) - $start) * 1000, 2);
            Log::error("AI Service Connection Exception: {$method} {$endpoint}", [
                'error' => $e->getMessage(),
                'latency_ms' => $duration,
            ]);
            return [
                'success' => false,
                'status' => 504,
                'data' => null,
                'latency' => $duration,
                'error' => 'Connection to AI Service failed or timed out: ' . $e->getMessage(),
            ];
        } catch (Exception $e) {
            $duration = round((microtime(true) - $start) * 1000, 2);
            Log::error("AI Service Unexpected Exception: {$method} {$endpoint}", [
                'error' => $e->getMessage(),
                'latency_ms' => $duration,
              ]);
            return [
                'success' => false,
                'status' => 500,
                'data' => null,
                'latency' => $duration,
                'error' => $e->getMessage(),
            ];
        }
    }
}
