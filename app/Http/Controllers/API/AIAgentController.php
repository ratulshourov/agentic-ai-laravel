<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\AI\AgentService;
use Illuminate\Http\Request;

class AIAgentController extends Controller
{
    public function chat(Request $request, AgentService $agent)
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        // Local LLM calls can take minutes; lift PHP's 30s execution limit.
        set_time_limit((int) config('ai.ollama.timeout') * 5 + 30);

        try {
            $answer = $agent->run($validated['message']);

            return response()->json([
                'success' => true,
                'question' => $validated['message'],
                'answer' => $answer,
            ]);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'AI Agent could not complete the request.',
            ], 500);
        }
    }
}
