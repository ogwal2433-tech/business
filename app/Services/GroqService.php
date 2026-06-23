<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GroqService
{
    protected string $apiKey;
    protected string $model;
    protected string $baseUri;

    public function __construct()
    {
        $this->apiKey = config('groq.api_key');
        $this->model = config('groq.model');
        $this->baseUri = config('groq.base_uri');
    }

    public function ask(string $message, array $context = [], array $tools = []): ?array
    {
        if (empty($this->apiKey)) {
            Log::warning('GROQ API key not configured');
            return null;
        }

        try {
            $systemPrompt = $this->buildSystemPrompt($context);

            $payload = [
                'model' => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $message],
                ],
                'temperature' => 0.7,
                'max_tokens' => 4096,
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(60)->post($this->baseUri . '/chat/completions', $payload);

            if ($response->successful()) {
                $data = $response->json();
                $choice = $data['choices'][0] ?? null;

                if (!$choice) return null;

                return [
                    'content' => $choice['message']['content'] ?? null,
                    'tool_calls' => $choice['message']['tool_calls'] ?? [],
                ];
            }

            Log::warning('GROQ API returned error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('GROQ API request failed: ' . $e->getMessage());
            return null;
        }
    }

    public function continue(array $messages, array $tools = []): ?array
    {
        if (empty($this->apiKey)) {
            Log::warning('GROQ API key not configured');
            return null;
        }

        try {
            $payload = [
                'model' => $this->model,
                'messages' => $messages,
                'temperature' => 0.7,
                'max_tokens' => 4096,
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(60)->post($this->baseUri . '/chat/completions', $payload);

            if ($response->successful()) {
                $data = $response->json();
                $choice = $data['choices'][0] ?? null;

                if (!$choice) return null;

                return [
                    'content' => $choice['message']['content'] ?? null,
                    'tool_calls' => $choice['message']['tool_calls'] ?? [],
                ];
            }

            Log::warning('GROQ API returned error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('GROQ API request failed: ' . $e->getMessage());
            return null;
        }
    }

    public function buildSystemPrompt(array $context): string
    {
        $base = "You are an intelligent business assistant for SmartBiz — a small business management system. "
              . "You have access to ALL business data through function calls. When the user asks a question, "
              . "use the available functions to retrieve the exact data needed. "
              . "Call functions one at a time — get the data you need, then answer the user. "
              . "If you need more data after the first call, make another function call. "
              . "When discussing money, always mention amounts in UGX. "
              . "Respond in the same language the user used to ask the question.\n\n"
              . "To call a function, output it on its own line like this:\n"
              . "<function=function_name={\"arg\":\"value\"}</function>\n"
              . "Example: <function=query_employees={\"status\":\"active\"}</function>\n"
              . "The function names and available arguments will be listed in your system prompt above.";

        if (!empty($context)) {
            $base .= "\n\nCurrent business context:\n";
            foreach ($context as $key => $value) {
                $base .= "- $key: $value\n";
            }
        }

        return $base;
    }
}
