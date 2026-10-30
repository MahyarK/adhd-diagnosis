<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;
use Throwable;

final class EnergyCrashAiGuide
{
    /**
     * @param  array{state?: string|null, must?: string|null, body?: string|null, drop?: string|null, support?: string|null, time?: string|null, locale: string}  $input
     * @return array{available: bool, title: string, first: string, body: string, steps: array<int, string>, drop: string, script: string, stop: string}
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
                            'name' => 'adhd_energy_crash',
                            'schema' => [
                                'type' => 'object',
                                'additionalProperties' => false,
                                'properties' => [
                                    'title' => ['type' => 'string'],
                                    'first' => ['type' => 'string'],
                                    'body' => ['type' => 'string'],
                                    'steps' => [
                                        'type' => 'array',
                                        'items' => ['type' => 'string'],
                                        'minItems' => 3,
                                        'maxItems' => 5,
                                    ],
                                    'drop' => ['type' => 'string'],
                                    'script' => ['type' => 'string'],
                                    'stop' => ['type' => 'string'],
                                ],
                                'required' => ['title', 'first', 'body', 'steps', 'drop', 'script', 'stop'],
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
                'body' => $this->clean($decoded['body'] ?? ''),
                'steps' => $this->cleanList($decoded['steps'] ?? []),
                'drop' => $this->clean($decoded['drop'] ?? ''),
                'script' => $this->clean($decoded['script'] ?? ''),
                'stop' => $this->clean($decoded['stop'] ?? ''),
            ];

            if ($plan['title'] === '' || $plan['first'] === '' || $plan['body'] === '' || $plan['steps'] === [] || $plan['drop'] === '' || $plan['script'] === '' || $plan['stop'] === '') {
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
            'You are an ADHD-friendly low-capacity planning assistant inside a self-help app.',
            'Return JSON only with title, first, body, steps, drop, script, and stop.',
            'Use the user locale if possible.',
            'Do not diagnose, do not mention being an AI, and do not make medical claims.',
            'Do not encourage pushing through exhaustion; reduce scope and protect body needs.',
            'Make one survival plan for the day the user actually has.',
            'Keep steps tiny, concrete, non-shaming, and useful inside the app.',
        ]);
    }

    /**
     * @param  array{state?: string|null, must?: string|null, body?: string|null, drop?: string|null, support?: string|null, time?: string|null, locale: string}  $input
     * @return array{available: bool, title: string, first: string, body: string, steps: array<int, string>, drop: string, script: string, stop: string}
     */
    private function fallback(array $input): array
    {
        $state = $this->clean($input['state'] ?? 'empty');
        $must = $this->clean($input['must'] ?? '') ?: __('assessment.tools.energy.defaults.must');
        $body = $this->clean($input['body'] ?? 'water');
        $drop = $this->clean($input['drop'] ?? '') ?: __('assessment.tools.energy.defaults.drop');
        $support = $this->clean($input['support'] ?? '') ?: __('assessment.tools.energy.defaults.support');
        $time = $this->clean($input['time'] ?? '10');

        return [
            'available' => false,
            'title' => __('assessment.tools.energy.ai_fallback.title', ['state' => $this->stateLabel($state)]),
            'first' => __('assessment.tools.energy.ai_fallback.first', [
                'time' => $this->timeLabel($time),
                'must' => $must,
            ]),
            'body' => $this->bodyReset($body),
            'steps' => $this->steps($state),
            'drop' => __('assessment.tools.energy.ai_fallback.drop', ['drop' => $drop]),
            'script' => __('assessment.tools.energy.ai_fallback.script', [
                'support' => $support,
                'must' => $must,
                'time' => $this->timeLabel($time),
            ]),
            'stop' => __('assessment.tools.energy.ai_fallback.stop'),
        ];
    }

    private function bodyReset(string $body): string
    {
        $key = in_array($body, ['food', 'water', 'meds', 'rest', 'movement', 'shower'], true) ? $body : 'water';

        return __('assessment.tools.energy.ai_fallback.body.'.$key);
    }

    /**
     * @return array<int, string>
     */
    private function steps(string $state): array
    {
        $key = in_array($state, ['empty', 'overwhelmed', 'stuck', 'wired'], true) ? $state : 'empty';

        return [
            __('assessment.tools.energy.ai_fallback.steps.'.$key.'.one'),
            __('assessment.tools.energy.ai_fallback.steps.'.$key.'.two'),
            __('assessment.tools.energy.ai_fallback.steps.'.$key.'.three'),
        ];
    }

    private function stateLabel(string $state): string
    {
        $labels = trans('assessment.tools.energy.states');

        return is_array($labels) && isset($labels[$state]) ? $labels[$state] : __('assessment.tools.energy.states.empty');
    }

    private function timeLabel(string $time): string
    {
        $labels = trans('assessment.tools.energy.times');

        return is_array($labels) && isset($labels[$time]) ? $labels[$time] : __('assessment.tools.energy.times.10');
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
