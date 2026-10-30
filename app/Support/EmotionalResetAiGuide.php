<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;
use Throwable;

final class EmotionalResetAiGuide
{
    /**
     * @param  array{trigger?: string|null, intensity?: string|null, body?: string|null, story?: string|null, next?: string|null, support?: string|null, locale: string}  $input
     * @return array{available: bool, title: string, first: string, ground: array<int, string>, reframe: string, next: string, script: string, stop: string}
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
                            'name' => 'adhd_emotional_reset',
                            'schema' => [
                                'type' => 'object',
                                'additionalProperties' => false,
                                'properties' => [
                                    'title' => ['type' => 'string'],
                                    'first' => ['type' => 'string'],
                                    'ground' => [
                                        'type' => 'array',
                                        'items' => ['type' => 'string'],
                                        'minItems' => 3,
                                        'maxItems' => 5,
                                    ],
                                    'reframe' => ['type' => 'string'],
                                    'next' => ['type' => 'string'],
                                    'script' => ['type' => 'string'],
                                    'stop' => ['type' => 'string'],
                                ],
                                'required' => ['title', 'first', 'ground', 'reframe', 'next', 'script', 'stop'],
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
                'title' => $this->clean($decoded['title'] ?? ''),
                'first' => $this->clean($decoded['first'] ?? ''),
                'ground' => $this->cleanList($decoded['ground'] ?? []),
                'reframe' => $this->clean($decoded['reframe'] ?? ''),
                'next' => $this->clean($decoded['next'] ?? ''),
                'script' => $this->clean($decoded['script'] ?? ''),
                'stop' => $this->clean($decoded['stop'] ?? ''),
            ];

            if ($plan['title'] === '' || $plan['first'] === '' || $plan['ground'] === [] || $plan['reframe'] === '' || $plan['next'] === '' || $plan['script'] === '' || $plan['stop'] === '') {
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
            'You are an ADHD-friendly emotional reset assistant inside a self-help app.',
            'Return JSON only with title, first, ground, reframe, next, script, and stop.',
            'Use the user locale if possible.',
            'Do not diagnose, do not mention being an AI, and do not make medical claims.',
            'Do not handle crisis escalation here; keep it to non-emergency shame, overwhelm, rejection, guilt, or conflict reset support.',
            'Use gentle, concrete, low-shame wording that can be acted on while dysregulated.',
            'Make the first reset body-based and the next action tiny.',
            'Keep every item short and suitable for display inside the app.',
        ]);
    }

    /**
     * @param  array{trigger?: string|null, intensity?: string|null, body?: string|null, story?: string|null, next?: string|null, support?: string|null, locale: string}  $input
     * @return array{available: bool, title: string, first: string, ground: array<int, string>, reframe: string, next: string, script: string, stop: string}
     */
    private function fallback(array $input): array
    {
        $trigger = $this->clean($input['trigger'] ?? '') ?: __('assessment.tools.emotion.defaults.trigger');
        $intensity = $this->clean($input['intensity'] ?? 'medium');
        $body = $this->clean($input['body'] ?? 'unknown');
        $story = $this->clean($input['story'] ?? '') ?: __('assessment.tools.emotion.ai_fallback.story_default');
        $next = $this->clean($input['next'] ?? '') ?: __('assessment.tools.emotion.ai_fallback.next_default');
        $support = $this->clean($input['support'] ?? '') ?: __('assessment.tools.emotion.ai_fallback.support_default');

        return [
            'available' => false,
            'title' => __('assessment.tools.emotion.ai_fallback.title', ['trigger' => $trigger]),
            'first' => $this->bodyReset($body),
            'ground' => $this->grounding($intensity),
            'reframe' => __('assessment.tools.emotion.ai_fallback.reframe', ['story' => $story]),
            'next' => __('assessment.tools.emotion.ai_fallback.next', ['next' => $next]),
            'script' => __('assessment.tools.emotion.ai_fallback.script', [
                'support' => $support,
                'trigger' => $trigger,
            ]),
            'stop' => __('assessment.tools.emotion.ai_fallback.stop'),
        ];
    }

    private function bodyReset(string $body): string
    {
        $key = in_array($body, ['tight', 'restless', 'numb', 'tired', 'unknown'], true) ? $body : 'unknown';

        return __('assessment.tools.emotion.ai_fallback.body.'.$key);
    }

    /**
     * @return array<int, string>
     */
    private function grounding(string $intensity): array
    {
        $key = in_array($intensity, ['low', 'medium', 'high'], true) ? $intensity : 'medium';

        return [
            __('assessment.tools.emotion.ai_fallback.ground.'.$key.'.one'),
            __('assessment.tools.emotion.ai_fallback.ground.'.$key.'.two'),
            __('assessment.tools.emotion.ai_fallback.ground.'.$key.'.three'),
        ];
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
