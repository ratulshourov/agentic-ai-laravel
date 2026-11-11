<?php

namespace App\AI\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;
use Throwable;

class AgentService
{
    public function __construct(
        protected ToolRegistry $registry
    ) {}

    public function chat(string $question): string
    {
        $messages = [
            [
                'role' => 'system',
                'content' => '
                    You are a helpful Packly business assistant.
                    Use available tools to retrieve actual business data.
                    Never invent product or stock information.
                ',
            ],
            [
                'role' => 'user',
                'content' => $question,
            ],
        ];

        $tools = $this->registry->definitions();

        $maxIterations = 5;

        for ($i = 0; $i < $maxIterations; $i++) {
            $response = Http::baseUrl(
                config('ai.ollama.base_url')
            )
                ->timeout(config('ai.ollama.timeout'))
                ->post('/api/chat', [
                    'model' => config('ai.ollama.model'),
                    'messages' => $messages,
                    'tools' => $tools,
                    'stream' => false,
                ]);

            $response->throw();

            $assistantMessage = $response->json('message');

            if (!$assistantMessage) {
                throw new RuntimeException(
                    'Invalid Ollama response.'
                );
            }

            $messages[] = $assistantMessage;

            $toolCalls = $assistantMessage['tool_calls'] ?? [];

            // No more tool calls: return final answer.
            if (empty($toolCalls)) {
                return $assistantMessage['content'] ?? '';
            }

            foreach ($toolCalls as $toolCall) {
                $function = $toolCall['function'] ?? [];

                $toolName = $function['name'] ?? null;
                $arguments = $function['arguments'] ?? [];

                if (!$toolName || !is_array($arguments)) {
                    $result = [
                        'success' => false,
                        'message' => 'Invalid tool call.',
                    ];
                } else {
                    try {
                        $result = $this->registry->execute(
                            $toolName,
                            $arguments
                        );
                    } catch (Throwable $exception) {
                        report($exception);

                        $result = [
                            'success' => false,
                            'message' => 'Tool execution failed.',
                        ];
                    }
                }

                $messages[] = [
                    'role' => 'tool',
                    'content' => json_encode(
                        $result,
                        JSON_UNESCAPED_UNICODE
                    ),
                ];
            }
        }

        return 'Maximum tool execution limit reached. Please try a simpler request.';
    }
}
