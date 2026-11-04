<?php

namespace App\AI;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class OllamaClient
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
    public function chat(
        array $messages,
        ?string $model = null
    ): array {

        $baseUrl = rtrim(
            config('ai.ollama.base_url'),
            '/'
        );

        $timeout = (int) config('ai.ollama.timeout');

        // PHP's max_execution_time (30s by default) would kill the request
        // before a slow local model finishes; allow the HTTP timeout plus margin.
        set_time_limit($timeout + 15);

        $response = Http::timeout($timeout)
            ->connectTimeout(10)
            ->acceptJson()
            ->post($baseUrl . '/api/chat', [
                'model' => $model ?? config('ai.ollama.model'),
                'messages' => $messages,
                'stream' => false,
            ]);

        if (!$response->successful()) {
            throw new RuntimeException(
                'Ollama API error: ' . $response->body()
            );
        }

        $data = $response->json();

        if (!isset($data['message']['content'])) {
            throw new RuntimeException(
                'Invalid response from Ollama.'
            );
        }

        return [
            'model' => $data['model'] ?? null,
            'content' => $data['message']['content'],
            'done' => $data['done'] ?? false,
        ];
    }
}
