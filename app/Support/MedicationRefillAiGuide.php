<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;
use Throwable;

final class MedicationRefillAiGuide
{
    /**
     * @param  array{mode?: string|null, medicine?: string|null, supply?: string|null, blocker?: string|null, contact?: string|null, deadline?: string|null, locale: string}  $input
     * @return array{available: bool, title: string, first: string, steps: array<int, string>, script: string, questions: array<int, string>, safety: string, stop: string}
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
                            'name' => 'adhd_medication_refill',
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
                                    'script' => ['type' => 'string'],
                                    'questions' => [
                                        'type' => 'array',
                                        'items' => ['type' => 'string'],
                                        'minItems' => 2,
                                        'maxItems' => 5,
                                    ],
                                    'safety' => ['type' => 'string'],
                                    'stop' => ['type' => 'string'],
                                ],
                                'required' => ['title', 'first', 'steps', 'script', 'questions', 'safety', 'stop'],
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
                'script' => $this->clean($decoded['script'] ?? ''),
                'questions' => $this->cleanList($decoded['questions'] ?? []),
                'safety' => $this->clean($decoded['safety'] ?? ''),
                'stop' => $this->clean($decoded['stop'] ?? ''),
            ];

            if ($plan['title'] === '' || $plan['first'] === '' || $plan['steps'] === [] || $plan['script'] === '' || $plan['questions'] === [] || $plan['safety'] === '' || $plan['stop'] === '') {
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
            'You are an ADHD-friendly medication refill admin assistant inside a self-help app.',
            'Return JSON only with title, first, steps, script, questions, safety, and stop.',
            'Use the user locale if possible.',
            'Help organize refill admin steps, contact wording, questions, reminders, and follow-up.',
            'Do not diagnose, do not mention being an AI, and do not give medical advice.',
            'Never tell the user to change, skip, stop, restart, ration, or adjust medication.',
            'Tell the user to ask a pharmacist, prescriber, or qualified clinician for medication decisions.',
            'Keep steps tiny, concrete, non-shaming, and focused on using the app plan.',
        ]);
    }

    /**
     * @param  array{mode?: string|null, medicine?: string|null, supply?: string|null, blocker?: string|null, contact?: string|null, deadline?: string|null, locale: string}  $input
     * @return array{available: bool, title: string, first: string, steps: array<int, string>, script: string, questions: array<int, string>, safety: string, stop: string}
     */
    private function fallback(array $input): array
    {
        $mode = $this->clean($input['mode'] ?? 'running_low');
        $medicine = $this->clean($input['medicine'] ?? '') ?: __('assessment.tools.meds.defaults.medicine');
        $supply = $this->clean($input['supply'] ?? '') ?: __('assessment.tools.meds.defaults.supply');
        $blocker = $this->clean($input['blocker'] ?? '');
        $contact = $this->clean($input['contact'] ?? '') ?: __('assessment.tools.meds.defaults.contact');
        $deadline = $this->clean($input['deadline'] ?? '') ?: __('assessment.tools.meds.defaults.deadline');
        $steps = $this->steps($mode);

        if ($blocker !== '') {
            $steps[] = __('assessment.tools.meds.blocker_line', ['blocker' => $blocker]);
        }

        return [
            'available' => false,
            'title' => __('assessment.tools.meds.output_title'),
            'first' => __('assessment.tools.meds.first_action', [
                'medicine' => $medicine,
                'supply' => $supply,
            ]),
            'steps' => $steps,
            'script' => __('assessment.tools.meds.script', [
                'medicine' => $medicine,
                'supply' => $supply,
                'contact' => $contact,
            ]),
            'questions' => array_map(
                fn (string $question): string => str_replace(':deadline', $deadline, $question),
                $this->questions(),
            ),
            'safety' => __('assessment.tools.meds.safety_note'),
            'stop' => __('assessment.tools.meds.stop_rule'),
        ];
    }

    /**
     * @return array<int, string>
     */
    private function steps(string $mode): array
    {
        $steps = trans('assessment.tools.meds.mode_steps');
        $key = in_array($mode, ['running_low', 'out', 'blocked', 'routine'], true) ? $mode : 'running_low';

        return is_array($steps) && isset($steps[$key]) && is_array($steps[$key])
            ? array_values($steps[$key])
            : [];
    }

    /**
     * @return array<int, string>
     */
    private function questions(): array
    {
        $questions = trans('assessment.tools.meds.questions');

        return is_array($questions) ? array_values($questions) : [];
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
