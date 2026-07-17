<?php

namespace App\Http\Controllers;

use App\Services\AIService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AIServiceController extends Controller
{
    protected AIService $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Show the AI Service Connection check panel.
     */
    public function index(): View
    {
        $status = $this->aiService->getStatusForDashboard();
        return view('ai-service.index', compact('status'));
    }

    /**
     * Execute a ping request to the FastAPI service.
     */
    public function ping(Request $request): JsonResponse
    {
        $payload = $request->input('payload', []);
        $result  = $this->aiService->ping($payload);
        return response()->json($result);
    }

    /**
     * Execute a health request to the FastAPI service.
     */
    public function health(): JsonResponse
    {
        $result = $this->aiService->health();
        return response()->json($result);
    }

    /**
     * Execute an echo request to the FastAPI service.
     */
    public function echo(Request $request): JsonResponse
    {
        $payload = $request->input('payload', []);
        $result  = $this->aiService->echo($payload);
        return response()->json($result);
    }
}
