<?php

namespace App\Http\Controllers;

use App\Services\AIService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AiGatewayController extends Controller
{
    protected AIService $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Show the AI Gateway control panel.
     */
    public function index(): View
    {
        $providersResult = $this->aiService->providers();
        $healthResult    = $this->aiService->health();

        $activeProvider = $providersResult['data']['active_provider'] ?? 'unknown';
        $providers      = $providersResult['data']['providers'] ?? [];
        $gatewayHealth  = $healthResult['data'] ?? [];

        return view('ai-gateway.index', compact('activeProvider', 'providers', 'gatewayHealth', 'providersResult', 'healthResult'));
    }

    /**
     * Get live health status of the gateway via AJAX.
     */
    public function health(): JsonResponse
    {
        $result = $this->aiService->health();
        if ($result['success']) {
            $data = $result['data'];
            $data['latency'] = $result['latency'];
            return response()->json($data);
        }
        return response()->json($result);
    }

    /**
     * Get live list of providers via AJAX.
     */
    public function providers(): JsonResponse
    {
        $result = $this->aiService->providers();
        if ($result['success']) {
            $data = $result['data'];
            $data['latency'] = $result['latency'];
            return response()->json($data);
        }
        return response()->json($result);
    }

    /**
     * Execute a test prompt query via AJAX.
     */
    public function testPrompt(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'prompt' => 'required|string|max:5000',
        ]);

        $result = $this->aiService->testPrompt($validated['prompt']);
        if ($result['success']) {
            $data = $result['data'];
            $data['latency'] = $result['latency'];
            return response()->json($data);
        }
        return response()->json($result);
    }
}
