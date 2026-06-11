<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;
use Throwable;

final class CheckInAiGuide
{
    /**
     * @param  array{feeling: string, energy: string, pressure: string, message?: string|null, locale: string}  $input
     * @return array{available: bool, response: string, first: string}
     */
    public function guide(array $input): array
    {
        $key = config('services.openai.key');

        if (! is_string($key) || trim($key) === '') {
            return [
                'available' => false,
                'response' => __('assessment.tools.checkin.ai_unavailable'),
                'first' => __('assessment.tools.checkin.ai_unavailable_first'),
            ];
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
                            'name' => 'adhd_check_in_guidance',
                            'schema' => [
                                'type' => 'object',
                                'additionalProperties' => false,
                                'properties' => [
                                    'response' => ['type' => 'string'],
                                    'first' => ['type' => 'string'],
                                ],
                                'required' => ['response', 'first'],
                            ],
                        ],
                    ],
                ]);

            if (! $response->successful()) {
                return $this->fallback();
            }

            $text = $response->json('output_text');
            $decoded = is_string($text) ? json_decode($text, true) : null;

            if (! is_array($decoded)) {
                return $this->fallback();
            }

            $guidedResponse = $this->clean($decoded['response'] ?? '');
            $guidedFirst = $this->clean($decoded['first'] ?? '');

            if ($guidedResponse === '' || $guidedFirst === '') {
                return $this->fallback();
            }

            return [
                'available' => true,
                'response' => $guidedResponse,
                'first' => $guidedFirst,
            ];
        } catch (Throwable) {
            return $this->fallback();
        }
    }

    private function systemPrompt(): string
    {
        return implode(' ', [
            'You are a gentle ADHD support guide inside a self-help app.',
            'Return JSON only with response and first.',
            'Use the user locale if possible.',
            'Be warm, plain-language, non-shaming, and specific.',
            'Do not diagnose, do not claim medical authority, and do not mention being an AI.',
            'If pressure is unsafe, tell the user to use live support or emergency/crisis help now.',
            'Keep response under 55 words and first under 28 words.',
        ]);
    }

    /**
     * @return array{available: bool, response: string, first: string}
     */
    private function fallback(): array
    {
        return [
            'available' => false,
            'response' => __('assessment.tools.checkin.ai_error'),
            'first' => __('assessment.tools.checkin.ai_error_first'),
        ];
    }

    private function clean(mixed $value): string
    {
        $text = trim(is_string($value) ? $value : '');

        return $text;
    }
}
