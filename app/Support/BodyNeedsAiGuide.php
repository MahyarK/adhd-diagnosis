<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;
use Throwable;

final class BodyNeedsAiGuide
{
    /**
     * @param  array{food?: string|null, water?: string|null, meds?: string|null, sleep?: string|null, signal?: string|null, task?: string|null, locale: string}  $input
     * @return array{available: bool, title: string, first: string, steps: array<int, string>, checks: array<int, string>, task: string, stop: string}
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
                            'name' => 'adhd_body_needs',
                            'schema' => [
                                'type' => 'object',
                                'additionalProperties' => false,
                                'properties' => [
                                    'title' => ['type' => 'string'],
                                    'first' => ['type' => 'string'],
                                    'steps' => [
                                        'type' => 'array',
                                        'items' => ['type' => 'string'],
                                        'minItems' => 3,
                                        'maxItems' => 5,
                                    ],
                                    'checks' => [
                                        'type' => 'array',
                                        'items' => ['type' => 'string'],
                                        'minItems' => 3,
                                        'maxItems' => 5,
                                    ],
                                    'task' => ['type' => 'string'],
                                    'stop' => ['type' => 'string'],
                                ],
                                'required' => ['title', 'first', 'steps', 'checks', 'task', 'stop'],
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
                'steps' => $this->cleanList($decoded['steps'] ?? []),
                'checks' => $this->cleanList($decoded['checks'] ?? []),
                'task' => $this->clean($decoded['task'] ?? ''),
                'stop' => $this->clean($decoded['stop'] ?? ''),
            ];

            if ($plan['title'] === '' || $plan['first'] === '' || $plan['steps'] === [] || $plan['checks'] === [] || $plan['task'] === '' || $plan['stop'] === '') {
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
            'You are an ADHD-friendly body-needs check-in assistant inside a self-help app.',
            'Return JSON only with title, first, steps, checks, task, and stop.',
            'Use the user locale if possible.',
            'Do not diagnose, do not mention being an AI, and do not make medical claims.',
            'Do not give medication dosing advice; only suggest checking the normal prescribed routine or reminder.',
            'Prioritize food, water, sleep capacity, body signals, and making the next task smaller.',
            'Keep each item short, concrete, low-shame, and usable inside the app.',
        ]);
    }

    /**
     * @param  array{food?: string|null, water?: string|null, meds?: string|null, sleep?: string|null, signal?: string|null, task?: string|null, locale: string}  $input
     * @return array{available: bool, title: string, first: string, steps: array<int, string>, checks: array<int, string>, task: string, stop: string}
     */
    private function fallback(array $input): array
    {
        $food = $this->clean($input['food'] ?? 'unknown');
        $water = $this->clean($input['water'] ?? 'unknown');
        $meds = $this->clean($input['meds'] ?? 'unknown');
        $sleep = $this->clean($input['sleep'] ?? 'unknown');
        $signal = $this->clean($input['signal'] ?? 'unknown');
        $task = $this->clean($input['task'] ?? '') ?: __('assessment.tools.body.defaults.task');

        return [
            'available' => false,
            'title' => __('assessment.tools.body.ai_fallback.title'),
            'first' => match ($food) {
                'empty' => __('assessment.tools.body.ai_fallback.first.empty'),
                'snack' => __('assessment.tools.body.ai_fallback.first.snack'),
                'okay' => __('assessment.tools.body.ai_fallback.first.okay'),
                default => __('assessment.tools.body.ai_fallback.first.unknown'),
            },
            'steps' => [
                $this->waterStep($water),
                $this->medsStep($meds),
                $this->sleepStep($sleep),
                $this->signalStep($signal),
            ],
            'checks' => [
                __('assessment.tools.body.ai_fallback.checks.food'),
                __('assessment.tools.body.ai_fallback.checks.water'),
                __('assessment.tools.body.ai_fallback.checks.body'),
            ],
            'task' => __('assessment.tools.body.ai_fallback.task', ['task' => $task]),
            'stop' => __('assessment.tools.body.ai_fallback.stop'),
        ];
    }

    private function waterStep(string $water): string
    {
        return match ($water) {
            'low' => __('assessment.tools.body.ai_fallback.water.low'),
            'some' => __('assessment.tools.body.ai_fallback.water.some'),
            'okay' => __('assessment.tools.body.ai_fallback.water.okay'),
            default => __('assessment.tools.body.ai_fallback.water.unknown'),
        };
    }

    private function medsStep(string $meds): string
    {
        return match ($meds) {
            'needed' => __('assessment.tools.body.ai_fallback.meds.needed'),
            'done' => __('assessment.tools.body.ai_fallback.meds.done'),
            'none' => __('assessment.tools.body.ai_fallback.meds.none'),
            default => __('assessment.tools.body.ai_fallback.meds.unknown'),
        };
    }

    private function sleepStep(string $sleep): string
    {
        return match ($sleep) {
            'low' => __('assessment.tools.body.ai_fallback.sleep.low'),
            'medium' => __('assessment.tools.body.ai_fallback.sleep.medium'),
            'okay' => __('assessment.tools.body.ai_fallback.sleep.okay'),
            default => __('assessment.tools.body.ai_fallback.sleep.unknown'),
        };
    }

    private function signalStep(string $signal): string
    {
        return match ($signal) {
            'tense' => __('assessment.tools.body.ai_fallback.signal.tense'),
            'restless' => __('assessment.tools.body.ai_fallback.signal.restless'),
            'foggy' => __('assessment.tools.body.ai_fallback.signal.foggy'),
            'pain' => __('assessment.tools.body.ai_fallback.signal.pain'),
            default => __('assessment.tools.body.ai_fallback.signal.unknown'),
        };
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
