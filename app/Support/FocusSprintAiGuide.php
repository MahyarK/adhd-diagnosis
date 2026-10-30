<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;
use Throwable;

final class FocusSprintAiGuide
{
    /**
     * @param  array{task?: string|null, mode?: string|null, minutes?: string|null, distraction?: string|null, support?: string|null, reward?: string|null, locale: string}  $input
     * @return array{available: bool, title: string, start: string, steps: array<int, string>, distraction: string, support: string, reward: string, stop: string}
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
                            'name' => 'adhd_focus_sprint',
                            'schema' => [
                                'type' => 'object',
                                'additionalProperties' => false,
                                'properties' => [
                                    'title' => ['type' => 'string'],
                                    'start' => ['type' => 'string'],
                                    'steps' => [
                                        'type' => 'array',
                                        'items' => ['type' => 'string'],
                                        'minItems' => 3,
                                        'maxItems' => 5,
                                    ],
                                    'distraction' => ['type' => 'string'],
                                    'support' => ['type' => 'string'],
                                    'reward' => ['type' => 'string'],
                                    'stop' => ['type' => 'string'],
                                ],
                                'required' => ['title', 'start', 'steps', 'distraction', 'support', 'reward', 'stop'],
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
                'start' => $this->clean($decoded['start'] ?? ''),
                'steps' => $this->cleanList($decoded['steps'] ?? []),
                'distraction' => $this->clean($decoded['distraction'] ?? ''),
                'support' => $this->clean($decoded['support'] ?? ''),
                'reward' => $this->clean($decoded['reward'] ?? ''),
                'stop' => $this->clean($decoded['stop'] ?? ''),
            ];

            if ($plan['title'] === '' || $plan['start'] === '' || $plan['steps'] === [] || $plan['distraction'] === '' || $plan['support'] === '' || $plan['reward'] === '' || $plan['stop'] === '') {
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
            'You are an ADHD-friendly focus sprint coach inside a self-help app.',
            'Return JSON only with title, start, steps, distraction, support, reward, and stop.',
            'Use the user locale if possible.',
            'Make one short timer-based sprint, not a whole productivity system.',
            'Do not diagnose, do not mention being an AI, and do not make medical claims.',
            'The start cue must be concrete and possible in two minutes or less.',
            'Use the selected timer length, support, distraction rule, and reward when useful.',
            'Keep each item short, direct, and non-shaming.',
        ]);
    }

    /**
     * @param  array{task?: string|null, mode?: string|null, minutes?: string|null, distraction?: string|null, support?: string|null, reward?: string|null, locale: string}  $input
     * @return array{available: bool, title: string, start: string, steps: array<int, string>, distraction: string, support: string, reward: string, stop: string}
     */
    private function fallback(array $input): array
    {
        $task = $this->clean($input['task'] ?? '') ?: __('assessment.tools.focus.defaults.task');
        $mode = $this->clean($input['mode'] ?? 'start');
        $minutes = $this->clean($input['minutes'] ?? '10');
        $distraction = $this->clean($input['distraction'] ?? '') ?: __('assessment.tools.focus.ai_fallback.distraction_default');
        $support = $this->clean($input['support'] ?? 'timer');
        $reward = $this->clean($input['reward'] ?? '') ?: __('assessment.tools.focus.ai_fallback.reward_default');

        return [
            'available' => false,
            'title' => __('assessment.tools.focus.ai_fallback.title', ['task' => $task]),
            'start' => __('assessment.tools.focus.ai_fallback.start', [
                'minutes' => $this->minuteLabel($minutes),
                'task' => $task,
            ]),
            'steps' => $this->steps($mode),
            'distraction' => __('assessment.tools.focus.ai_fallback.distraction', ['distraction' => $distraction]),
            'support' => __('assessment.tools.focus.ai_fallback.support', ['support' => $this->supportLabel($support)]),
            'reward' => __('assessment.tools.focus.ai_fallback.reward', ['reward' => $reward]),
            'stop' => __('assessment.tools.focus.ai_fallback.stop'),
        ];
    }

    /**
     * @return array<int, string>
     */
    private function steps(string $mode): array
    {
        $key = in_array($mode, ['start', 'continue', 'boring', 'avoidance'], true) ? $mode : 'start';

        return [
            __('assessment.tools.focus.ai_fallback.steps.'.$key.'.one'),
            __('assessment.tools.focus.ai_fallback.steps.'.$key.'.two'),
            __('assessment.tools.focus.ai_fallback.steps.'.$key.'.three'),
        ];
    }

    private function minuteLabel(string $minutes): string
    {
        $labels = trans('assessment.tools.focus.minutes');

        return is_array($labels) && isset($labels[$minutes]) ? $labels[$minutes] : __('assessment.tools.focus.minutes.10');
    }

    private function supportLabel(string $support): string
    {
        $labels = trans('assessment.tools.focus.supports');

        return is_array($labels) && isset($labels[$support]) ? $labels[$support] : __('assessment.tools.focus.supports.timer');
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
