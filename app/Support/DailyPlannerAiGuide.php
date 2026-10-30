<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;
use Throwable;

final class DailyPlannerAiGuide
{
    /**
     * @param  array{must?: string|null, should?: string|null, body?: string|null, scary?: string|null, recovery?: string|null, locale: string}  $input
     * @return array{available: bool, must: array<int, string>, should: array<int, string>, body: string, scary: string, recovery: string}
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
                            'name' => 'adhd_daily_planner',
                            'schema' => [
                                'type' => 'object',
                                'additionalProperties' => false,
                                'properties' => [
                                    'must' => [
                                        'type' => 'array',
                                        'items' => ['type' => 'string'],
                                        'minItems' => 1,
                                        'maxItems' => 3,
                                    ],
                                    'should' => [
                                        'type' => 'array',
                                        'items' => ['type' => 'string'],
                                        'minItems' => 1,
                                        'maxItems' => 4,
                                    ],
                                    'body' => ['type' => 'string'],
                                    'scary' => ['type' => 'string'],
                                    'recovery' => ['type' => 'string'],
                                ],
                                'required' => ['must', 'should', 'body', 'scary', 'recovery'],
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

            $plan = [
                'available' => true,
                'must' => $this->cleanList($decoded['must'] ?? []),
                'should' => $this->cleanList($decoded['should'] ?? []),
                'body' => $this->clean($decoded['body'] ?? ''),
                'scary' => $this->clean($decoded['scary'] ?? ''),
                'recovery' => $this->clean($decoded['recovery'] ?? ''),
            ];

            if ($plan['must'] === [] || $plan['body'] === '' || $plan['scary'] === '' || $plan['recovery'] === '') {
                return $this->fallback($input);
            }

            return $plan;
        } catch (Throwable) {
            return $this->fallback($input);
        }
    }

    private function systemPrompt(): string
    {
        return implode(' ', [
            'You are an ADHD-friendly daily planning assistant inside a self-help app.',
            'Return JSON only with must, should, body, scary, and recovery.',
            'Use the user locale if possible.',
            'Make a realistic minimum viable day, not an ideal schedule.',
            'Do not diagnose, do not mention being an AI, and do not make medical claims.',
            'Must items should be few and concrete. Should items must be optional.',
            'Body support should protect food, water, medication routine, rest, movement, or hygiene.',
            'Scary task should be a two-minute contact step. Recovery should be a restart rule.',
            'Keep each item under 18 words.',
        ]);
    }

    /**
     * @param  array{must?: string|null, should?: string|null, body?: string|null, scary?: string|null, recovery?: string|null, locale: string}  $input
     * @return array{available: bool, must: array<int, string>, should: array<int, string>, body: string, scary: string, recovery: string}
     */
    private function fallback(array $input): array
    {
        $must = $this->lines($input['must'] ?? '');
        $should = $this->lines($input['should'] ?? '');
        $body = $this->clean($input['body'] ?? '');
        $scary = $this->clean($input['scary'] ?? '');
        $recovery = $this->clean($input['recovery'] ?? '');

        return [
            'available' => false,
            'must' => array_slice($must !== [] ? $must : [__('assessment.tools.planner.ai_fallback.must')], 0, 3),
            'should' => array_slice($should !== [] ? $should : [__('assessment.tools.planner.ai_fallback.should')], 0, 4),
            'body' => $body !== '' ? $body : __('assessment.tools.planner.ai_fallback.body'),
            'scary' => $scary !== '' ? __('assessment.tools.planner.ai_fallback.scary_with_task', ['task' => $scary]) : __('assessment.tools.planner.ai_fallback.scary'),
            'recovery' => $recovery !== '' ? $recovery : __('assessment.tools.planner.ai_fallback.recovery'),
        ];
    }

    /**
     * @return array<int, string>
     */
    private function lines(mixed $value): array
    {
        $text = $this->clean($value);

        if ($text === '') {
            return [];
        }

        return array_values(array_filter(array_map(
            fn (string $line): string => trim($line),
            preg_split('/\R/', $text) ?: [],
        )));
    }

    /**
     * @return array<int, string>
     */
    private function cleanList(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }

        return array_values(array_filter(array_map(fn (mixed $item): string => $this->clean($item), $value)));
    }

    private function clean(mixed $value): string
    {
        return trim(is_string($value) ? $value : '');
    }
}
