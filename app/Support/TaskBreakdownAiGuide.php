<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;
use Throwable;

final class TaskBreakdownAiGuide
{
    /**
     * @param  array{dump?: string|null, focus: string, stuck?: string|null, done?: string|null, locale: string}  $input
     * @return array{available: bool, title: string, now: array<int, string>, microsteps: array<int, string>, hidden: array<int, string>, done: string, reset: string}
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
                            'name' => 'adhd_task_breakdown',
                            'schema' => [
                                'type' => 'object',
                                'additionalProperties' => false,
                                'properties' => [
                                    'title' => ['type' => 'string'],
                                    'now' => [
                                        'type' => 'array',
                                        'items' => ['type' => 'string'],
                                        'minItems' => 2,
                                        'maxItems' => 3,
                                    ],
                                    'microsteps' => [
                                        'type' => 'array',
                                        'items' => ['type' => 'string'],
                                        'minItems' => 4,
                                        'maxItems' => 6,
                                    ],
                                    'hidden' => [
                                        'type' => 'array',
                                        'items' => ['type' => 'string'],
                                        'minItems' => 2,
                                        'maxItems' => 4,
                                    ],
                                    'done' => ['type' => 'string'],
                                    'reset' => ['type' => 'string'],
                                ],
                                'required' => ['title', 'now', 'microsteps', 'hidden', 'done', 'reset'],
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
                'now' => $this->cleanList($decoded['now'] ?? []),
                'microsteps' => $this->cleanList($decoded['microsteps'] ?? []),
                'hidden' => $this->cleanList($decoded['hidden'] ?? []),
                'done' => $this->clean($decoded['done'] ?? ''),
                'reset' => $this->clean($decoded['reset'] ?? ''),
            ];

            if ($plan['title'] === '' || count($plan['microsteps']) < 3 || $plan['done'] === '' || $plan['reset'] === '') {
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
            'You are an ADHD-friendly task breakdown assistant inside a self-help app.',
            'Return JSON only with title, now, microsteps, hidden, done, and reset.',
            'Use the user locale if possible.',
            'Make the plan concrete, tiny, non-shaming, and usable inside the app.',
            'Do not diagnose, do not mention being an AI, and do not make medical claims.',
            'The first microstep must be physically visible or a direct app/document opening action.',
            'Keep each item under 18 words. Keep done and reset under 26 words each.',
        ]);
    }

    /**
     * @param  array{dump?: string|null, focus: string, stuck?: string|null, done?: string|null, locale: string}  $input
     * @return array{available: bool, title: string, now: array<int, string>, microsteps: array<int, string>, hidden: array<int, string>, done: string, reset: string}
     */
    private function fallback(array $input): array
    {
        $focus = $this->clean($input['focus'] ?? __('assessment.tools.task.empty_title'));
        $stuck = $this->clean($input['stuck'] ?? 'unclear');
        $done = $this->clean($input['done'] ?? '');

        $microsteps = match ($stuck) {
            'boring' => [
                __('assessment.tools.task.ai_fallback.steps.open'),
                __('assessment.tools.task.ai_fallback.steps.timer'),
                __('assessment.tools.task.ai_fallback.steps.stimulation'),
                __('assessment.tools.task.ai_fallback.steps.visible'),
                __('assessment.tools.task.ai_fallback.steps.choose'),
            ],
            'too_big' => [
                __('assessment.tools.task.ai_fallback.steps.open'),
                __('assessment.tools.task.ai_fallback.steps.smallest'),
                __('assessment.tools.task.ai_fallback.steps.setup'),
                __('assessment.tools.task.ai_fallback.steps.one_piece'),
                __('assessment.tools.task.ai_fallback.steps.restart'),
            ],
            'avoidance' => [
                __('assessment.tools.task.ai_fallback.steps.open'),
                __('assessment.tools.task.ai_fallback.steps.name_feeling'),
                __('assessment.tools.task.ai_fallback.steps.private'),
                __('assessment.tools.task.ai_fallback.steps.two_minutes'),
                __('assessment.tools.task.ai_fallback.steps.stop'),
            ],
            default => [
                __('assessment.tools.task.ai_fallback.steps.open'),
                __('assessment.tools.task.ai_fallback.steps.object'),
                __('assessment.tools.task.ai_fallback.steps.question'),
                __('assessment.tools.task.ai_fallback.steps.first_action'),
                __('assessment.tools.task.ai_fallback.steps.next_note'),
            ],
        };

        return [
            'available' => false,
            'title' => $focus,
            'now' => [
                __('assessment.tools.task.ai_fallback.now.timer'),
                __('assessment.tools.task.ai_fallback.now.touch', ['task' => $focus]),
            ],
            'microsteps' => $microsteps,
            'hidden' => [
                __('assessment.tools.task.ai_fallback.hidden.tool'),
                __('assessment.tools.task.ai_fallback.hidden.good_enough'),
                __('assessment.tools.task.ai_fallback.hidden.park'),
            ],
            'done' => $done !== '' ? $done : __('assessment.tools.task.ai_fallback.done'),
            'reset' => __('assessment.tools.task.ai_fallback.reset'),
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
