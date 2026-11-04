<?php

namespace App\AI;

use Illuminate\Support\Facades\Http;

class OllamaClient
{
    public function chat(array $messages, array $tools = []): array
    {
        $payload = [
            'model' => config('ai.ollama.model'),
            'messages' => $messages,
            'stream' => false,
        ];

        if ($tools !== []) {
            $payload['tools'] = $tools;
        }

        $response = Http::timeout(
            config('ai.ollama.timeout')
        )
            ->connectTimeout(500)
            ->post(
                config('ai.ollama.base_url') . '/api/chat',
                $payload
            );

        $response->throw();

        return $response->json();
    }
}
