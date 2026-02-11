<?php

namespace App\Http\Controllers\AI;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AIChatController extends Controller
{
    /**
     * Handle AI chat requests.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function handleChat(Request $request): JsonResponse
    {
        $userMessage = $request->input('message');

        // For now, return a mock response.
        // In a real scenario, this would interact with an AI service.
        $aiResponse = "You said: '" . $userMessage . "'. I'm currently under development, but I'm learning!";

        return response()->json([
            'reply' => $aiResponse,
        ]);
    }
}