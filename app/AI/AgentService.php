<?php

namespace App\AI;

class AgentService
{
    public function __construct(
        private OllamaClient $ollama,
        private ToolExecutor $executor
    ) {}

    public function run(string $question): string
    {
        $messages = [
            [
                'role' => 'system',
                'content' => 'You are a helpful AI agent.
                Use add_numbers, subtract_numbers, multiply_numbers and divide_numbers
                for addition, subtraction, multiplication and division.
                Never calculate arithmetic yourself when a tool is available.
                Give a clear final answer.',
            ],
            [
                'role' => 'user',
                'content' => $question,
            ],
        ];

        for ($i = 0; $i < 5; $i++) {

            $response = $this->ollama->chat(
                $messages,
                AgentTools::definitions()
            );

            $assistantMessage = $response['message'] ?? [];

            if (!$assistantMessage) {
                throw new \RuntimeException(
                    'Invalid response from Ollama.'
                );
            }

            $messages[] = $assistantMessage;

            $toolCalls = $assistantMessage['tool_calls'] ?? [];

            // No more tools: return final answer
            if (empty($toolCalls)) {
                return $assistantMessage['content'] ?? '';
            }

            // Execute requested tools
            foreach ($toolCalls as $toolCall) {

                $name = $toolCall['function']['name'] ?? '';

                $arguments = $toolCall['function']['arguments'] ?? [];

                if (is_string($arguments)) {
                    $arguments = json_decode($arguments, true) ?? [];
                }

                $result = $this->executor->execute(
                    $name,
                    $arguments
                );

                $messages[] = [
                    'role' => 'tool',
                    'content' => json_encode([
                        'result' => $result,
                    ]),
                ];
            }
        }

        throw new \RuntimeException(
            'Agent reached maximum tool iterations.'
        );
    }
}
