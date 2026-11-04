<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\AI\OllamaClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\ConnectionException;
use RuntimeException;
use Throwable;

class AIChatController extends Controller
{
    public function __construct(
        private OllamaClient $ollama
    ) {}

    public function chat(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:10000'],

            'system_prompt' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ]);

        $messages = [];

        $systemPrompt = $validated['system_prompt']
            ?? 'You are a helpful AI assistant.';

        $messages[] = [
            'role' => 'system',
            'content' => $systemPrompt,
        ];

        $messages[] = [
            'role' => 'user',
            'content' => $validated['message'],
        ];

        try {

            $result = $this->ollama->chat($messages);

            return response()->json([
                'success' => true,

                'data' => [
                    'question' => $validated['message'],
                    'answer' => $result['content'],
                    'model' => $result['model'],
                ],
            ]);
        } catch (ConnectionException $e) {

            Log::error('Ollama connection failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Cannot connect to Ollama.',
            ], 503);
        } catch (Throwable $e) {

            Log::error('AI chat failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'AI service failed.',
            ], 502);
        }
    }
}
