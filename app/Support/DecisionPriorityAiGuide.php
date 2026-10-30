<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;
use Throwable;

final class DecisionPriorityAiGuide
{
    /**
     * @param  array{options?: string|null, urgency?: string|null, energy?: string|null, consequence?: string|null, relief?: string|null, support?: string|null, locale: string}  $input
     * @return array{available: bool, title: string, chosen: string, why: string, first: string, steps: array<int, string>, parked: array<int, string>, script: string, stop: string}
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
                            'name' => 'adhd_decision_priority',
                            'schema' => [
                                'type' => 'object',
                                'additionalProperties' => false,
                                'properties' => [
                                    'title' => ['type' => 'string'],
                                    'chosen' => ['type' => 'string'],
                                    'why' => ['type' => 'string'],
                                    'first' => ['type' => 'string'],
                                    'steps' => [
                                        'type' => 'array',
                                        'items' => ['type' => 'string'],
                                        'minItems' => 3,
                                        'maxItems' => 5,
                                    ],
                                    'parked' => [
                                        'type' => 'array',
                                        'items' => ['type' => 'string'],
                                        'minItems' => 1,
                                        'maxItems' => 6,
                                    ],
                                    'script' => ['type' => 'string'],
                                    'stop' => ['type' => 'string'],
                                ],
                                'required' => ['title', 'chosen', 'why', 'first', 'steps', 'parked', 'script', 'stop'],
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
                'chosen' => $this->clean($decoded['chosen'] ?? ''),
                'why' => $this->clean($decoded['why'] ?? ''),
                'first' => $this->clean($decoded['first'] ?? ''),
                'steps' => $this->cleanList($decoded['steps'] ?? []),
                'parked' => $this->cleanList($decoded['parked'] ?? []),
                'script' => $this->clean($decoded['script'] ?? ''),
                'stop' => $this->clean($decoded['stop'] ?? ''),
            ];

            if ($plan['title'] === '' || $plan['chosen'] === '' || $plan['why'] === '' || $plan['first'] === '' || $plan['steps'] === [] || $plan['parked'] === [] || $plan['script'] === '' || $plan['stop'] === '') {
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
            'You are an ADHD-friendly decision support assistant inside a self-help app.',
            'Return JSON only with title, chosen, why, first, steps, parked, script, and stop.',
            'Use the user locale if possible.',
            'Pick one good-enough first option, not the perfect option.',
            'Do not diagnose, do not mention being an AI, and do not make medical claims.',
            'Park the other options explicitly so the user does not feel they disappeared.',
            'Make the first action doable in two minutes and keep each item short.',
        ]);
    }

    /**
     * @param  array{options?: string|null, urgency?: string|null, energy?: string|null, consequence?: string|null, relief?: string|null, support?: string|null, locale: string}  $input
     * @return array{available: bool, title: string, chosen: string, why: string, first: string, steps: array<int, string>, parked: array<int, string>, script: string, stop: string}
     */
    private function fallback(array $input): array
    {
        $options = $this->lines($input['options'] ?? '');
        $relief = $this->clean($input['relief'] ?? '');
        $chosen = $relief !== '' ? $relief : ($options[0] ?? __('assessment.tools.decision.defaults.chosen'));
        $parked = array_values(array_filter($options, fn (string $option): bool => $option !== $chosen));
        $support = $this->clean($input['support'] ?? '') ?: __('assessment.tools.decision.defaults.support');
        $energy = $this->clean($input['energy'] ?? 'low');
        $consequence = $this->clean($input['consequence'] ?? 'unsure');

        return [
            'available' => false,
            'title' => __('assessment.tools.decision.ai_fallback.title', ['choice' => $chosen]),
            'chosen' => $chosen,
            'why' => $this->reason($consequence),
            'first' => __('assessment.tools.decision.ai_fallback.first', ['choice' => $chosen]),
            'steps' => $this->steps($energy),
            'parked' => $parked !== [] ? $parked : [__('assessment.tools.decision.defaults.parked')],
            'script' => __('assessment.tools.decision.ai_fallback.script', [
                'support' => $support,
                'choice' => $chosen,
            ]),
            'stop' => __('assessment.tools.decision.ai_fallback.stop'),
        ];
    }

    private function reason(string $consequence): string
    {
        $key = in_array($consequence, ['deadline', 'money', 'health', 'relationship', 'relief', 'unsure'], true) ? $consequence : 'unsure';

        return __('assessment.tools.decision.ai_fallback.reasons.'.$key);
    }

    /**
     * @return array<int, string>
     */
    private function steps(string $energy): array
    {
        $key = in_array($energy, ['low', 'medium', 'high'], true) ? $energy : 'low';

        return [
            __('assessment.tools.decision.ai_fallback.steps.'.$key.'.one'),
            __('assessment.tools.decision.ai_fallback.steps.'.$key.'.two'),
            __('assessment.tools.decision.ai_fallback.steps.'.$key.'.three'),
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
