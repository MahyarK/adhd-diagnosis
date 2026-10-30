<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;
use Throwable;

final class GoalBuilderAiGuide
{
    /**
     * @param  array{goal: string, why?: string|null, blocker?: string|null, energy?: string|null, locale: string}  $input
     * @return array{available: bool, title: string, first: string, microsteps: array<int, string>, support: string, done: string}
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
                            'name' => 'adhd_goal_builder',
                            'schema' => [
                                'type' => 'object',
                                'additionalProperties' => false,
                                'properties' => [
                                    'title' => ['type' => 'string'],
                                    'first' => ['type' => 'string'],
                                    'microsteps' => [
                                        'type' => 'array',
                                        'items' => ['type' => 'string'],
                                        'minItems' => 4,
                                        'maxItems' => 6,
                                    ],
                                    'support' => ['type' => 'string'],
                                    'done' => ['type' => 'string'],
                                ],
                                'required' => ['title', 'first', 'microsteps', 'support', 'done'],
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
                'microsteps' => $this->cleanList($decoded['microsteps'] ?? []),
                'support' => $this->clean($decoded['support'] ?? ''),
                'done' => $this->clean($decoded['done'] ?? ''),
            ];

            if ($plan['title'] === '' || $plan['first'] === '' || count($plan['microsteps']) < 3 || $plan['support'] === '' || $plan['done'] === '') {
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
            'You are an ADHD-friendly goal coach inside a self-help app.',
            'Return JSON only with title, first, microsteps, support, and done.',
            'Use the user locale if possible.',
            'Turn the goal into something tiny, concrete, non-shaming, and usable inside the app.',
            'Do not diagnose, do not mention being an AI, and do not make medical claims.',
            'The first action must take two minutes or less and should involve opening, touching, writing, asking, or placing something visible.',
            'Keep each microstep under 16 words. Keep support and done under 28 words each.',
        ]);
    }

    /**
     * @param  array{goal: string, why?: string|null, blocker?: string|null, energy?: string|null, locale: string}  $input
     * @return array{available: bool, title: string, first: string, microsteps: array<int, string>, support: string, done: string}
     */
    private function fallback(array $input): array
    {
        $goal = $this->clean($input['goal'] ?? __('assessment.tools.goal.empty_title'));
        $energy = $this->clean($input['energy'] ?? 'low');
        $blocker = $this->clean($input['blocker'] ?? '');
        $why = $this->clean($input['why'] ?? '');

        $microsteps = [
            __('assessment.tools.goal.ai_fallback.steps.visible', ['goal' => $goal]),
            __('assessment.tools.goal.ai_fallback.steps.timer'),
            __('assessment.tools.goal.ai_fallback.steps.small'),
            __('assessment.tools.goal.ai_fallback.steps.pause'),
        ];

        if ($energy === 'medium') {
            array_splice($microsteps, 2, 0, [__('assessment.tools.goal.ai_fallback.steps.prepare')]);
        }

        if ($energy === 'urgent') {
            array_splice($microsteps, 1, 0, [__('assessment.tools.goal.ai_fallback.steps.good_enough')]);
            $microsteps[] = __('assessment.tools.goal.ai_fallback.steps.handoff');
        }

        $support = $blocker !== ''
            ? __('assessment.tools.goal.ai_fallback.support_with_blocker', ['blocker' => $blocker])
            : __('assessment.tools.goal.ai_fallback.support');

        $done = $why !== ''
            ? __('assessment.tools.goal.ai_fallback.done_with_why', ['why' => $why])
            : __('assessment.tools.goal.ai_fallback.done');

        return [
            'available' => false,
            'title' => $goal,
            'first' => __('assessment.tools.goal.ai_fallback.first', ['goal' => $goal]),
            'microsteps' => $microsteps,
            'support' => $support,
            'done' => $done,
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
