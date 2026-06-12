<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;
use Throwable;

final class DashboardAiGuide
{
    private const TOOLS = [
        'navigator', 'checkin', 'planner', 'body', 'food', 'sleep', 'task', 'focus',
        'motivation', 'accountability', 'decision', 'energy', 'emotion', 'safety',
        'money', 'followup', 'home', 'transition', 'time', 'digital', 'communication',
        'goal', 'routine', 'wins',
    ];

    /**
     * @param  array{message: string, checkin?: array<string, mixed>|null, locale: string}  $input
     * @return array{available: bool, response: string, first: string, tool: string}
     */
    public function guide(array $input): array
    {
        $key = config('services.openai.key');

        if (! is_string($key) || trim($key) === '') {
            return $this->fallback($input);
        }

        try {
            $response = Http::withToken($key)
                ->timeout(12)
                ->acceptJson()
                ->post('https://api.openai.com/v1/responses', [
                    'model' => config('services.openai.model'),
                    'input' => [
                        [
                            'role' => 'system',
                            'content' => $this->systemPrompt(),
                        ],
                        [
                            'role' => 'user',
                            'content' => json_encode($input, JSON_THROW_ON_ERROR),
                        ],
                    ],
                    'text' => [
                        'format' => [
                            'type' => 'json_schema',
                            'name' => 'adhd_dashboard_guidance',
                            'schema' => [
                                'type' => 'object',
                                'additionalProperties' => false,
                                'properties' => [
                                    'response' => ['type' => 'string'],
                                    'first' => ['type' => 'string'],
                                    'tool' => ['type' => 'string', 'enum' => self::TOOLS],
                                ],
                                'required' => ['response', 'first', 'tool'],
                            ],
                        ],
                    ],
                ]);

            if (! $response->successful()) {
                return $this->fallback($input);
            }

            $text = $response->json('output_text');
            $decoded = is_string($text) ? json_decode($text, true) : null;

            if (! is_array($decoded)) {
                return $this->fallback($input);
            }

            $guidedResponse = $this->clean($decoded['response'] ?? '');
            $guidedFirst = $this->clean($decoded['first'] ?? '');
            $tool = $this->tool($decoded['tool'] ?? null);

            if ($guidedResponse === '' || $guidedFirst === '') {
                return $this->fallback($input);
            }

            return [
                'available' => true,
                'response' => $guidedResponse,
                'first' => $guidedFirst,
                'tool' => $tool,
            ];
        } catch (Throwable) {
            return $this->fallback($input);
        }
    }

    private function systemPrompt(): string
    {
        return implode(' ', [
            'You are a gentle ADHD support guide inside a self-help dashboard.',
            'Return JSON only with response, first, and tool.',
            'Use the user locale if possible.',
            'Be warm, plain-language, non-shaming, and practical.',
            'Do not diagnose, do not claim medical authority, and do not mention being an AI.',
            'Pick exactly one tool key from the allowed enum.',
            'If the user sounds unsafe or in crisis, choose safety and tell them to use live support now.',
            'Keep response under 60 words and first under 24 words.',
        ]);
    }

    /**
     * @param  array{message: string, checkin?: array<string, mixed>|null, locale: string}  $input
     * @return array{available: bool, response: string, first: string, tool: string}
     */
    private function fallback(array $input): array
    {
        $tool = $this->localTool($input);

        return [
            'available' => false,
            'response' => __('assessment.tools.dashboard.guide.unavailable_response'),
            'first' => __('assessment.tools.dashboard.guide.unavailable_first'),
            'tool' => $tool,
        ];
    }

    private function localTool(array $input): string
    {
        $message = mb_strtolower((string) ($input['message'] ?? ''));
        $pressure = mb_strtolower((string) data_get($input, 'checkin.pressure', ''));

        return match (true) {
            str_contains($message, 'unsafe') || str_contains($message, 'crisis') || $pressure === 'unsafe' => 'safety',
            str_contains($message, 'bill') || str_contains($message, 'money') || str_contains($message, 'admin') || $pressure === 'money' => 'money',
            str_contains($message, 'late') || str_contains($message, 'forgot') || $pressure === 'late' => 'followup',
            str_contains($message, 'tired') || str_contains($message, 'sleep') || $pressure === 'body' => 'body',
            str_contains($message, 'start') || str_contains($message, 'focus') => 'focus',
            str_contains($message, 'home') || str_contains($message, 'clean') || $pressure === 'home' => 'home',
            default => 'navigator',
        };
    }

    private function tool(mixed $value): string
    {
        return is_string($value) && in_array($value, self::TOOLS, true) ? $value : 'navigator';
    }

    private function clean(mixed $value): string
    {
        return trim(is_string($value) ? $value : '');
    }
}
