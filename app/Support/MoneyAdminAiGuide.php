<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;
use Throwable;

final class MoneyAdminAiGuide
{
    /**
     * @param  array{task?: string|null, category?: string|null, urgency?: string|null, blocker?: string|null, contact?: string|null, outcome?: string|null, locale: string}  $input
     * @return array{available: bool, title: string, first: string, steps: array<int, string>, script: string, questions: array<int, string>, stop: string}
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
                            'name' => 'adhd_money_admin',
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
                                    'stop' => ['type' => 'string'],
                                ],
                                'required' => ['title', 'first', 'steps', 'script', 'questions', 'stop'],
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
                'stop' => $this->clean($decoded['stop'] ?? ''),
            ];

            if ($plan['title'] === '' || $plan['first'] === '' || $plan['steps'] === [] || $plan['script'] === '' || $plan['questions'] === [] || $plan['stop'] === '') {
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
            'You are an ADHD-friendly money and admin rescue assistant inside a self-help app.',
            'Return JSON only with title, first, steps, script, questions, and stop.',
            'Use the user locale if possible.',
            'Help the user reduce avoidance around one bill, form, message, appointment, insurance, benefits, or overdue admin task.',
            'Do not diagnose, do not mention being an AI, and do not give financial, legal, tax, benefits, or medical advice.',
            'Keep the plan focused on contact, clarity, records, questions, reminders, and one good-enough next action.',
            'Make every step small, concrete, non-shaming, and usable inside the app.',
        ]);
    }

    /**
     * @param  array{task?: string|null, category?: string|null, urgency?: string|null, blocker?: string|null, contact?: string|null, outcome?: string|null, locale: string}  $input
     * @return array{available: bool, title: string, first: string, steps: array<int, string>, script: string, questions: array<int, string>, stop: string}
     */
    private function fallback(array $input): array
    {
        $category = $this->category($this->clean($input['category'] ?? 'overdue'));
        $task = $this->clean($input['task'] ?? '') ?: $this->defaultTask($category);
        $outcome = $this->clean($input['outcome'] ?? '') ?: __('assessment.tools.money.defaults.outcome');
        $contact = $this->clean($input['contact'] ?? '') ?: $this->defaultContact($category);

        return [
            'available' => false,
            'title' => __('assessment.tools.money.output_title', ['task' => $task]),
            'first' => $this->first($task, $category, $this->clean($input['urgency'] ?? 'unsure'), $this->clean($input['blocker'] ?? '')),
            'steps' => $this->steps($category),
            'script' => $this->script($category, $contact, $task),
            'questions' => $this->questions($category, $outcome),
            'stop' => __('assessment.tools.money.stop_rule', ['outcome' => $outcome]),
        ];
    }

    private function first(string $task, string $category, string $urgency, string $blocker): string
    {
        $categories = trans('assessment.tools.money.categories');
        $urgencyNotes = trans('assessment.tools.money.urgency_notes');
        $categoryLabel = is_array($categories) && isset($categories[$category]) ? $categories[$category] : __('assessment.tools.money.categories.overdue');
        $urgencyKey = in_array($urgency, ['today', 'week', 'overdue', 'unsure'], true) ? $urgency : 'unsure';
        $urgencyText = is_array($urgencyNotes) && isset($urgencyNotes[$urgencyKey]) ? $urgencyNotes[$urgencyKey] : __('assessment.tools.money.urgency_notes.unsure');
        $blockerText = $blocker !== '' ? ' '.__('assessment.tools.money.blocker_note', ['blocker' => $blocker]) : '';

        return __('assessment.tools.money.first_action', [
            'task' => $task,
            'category' => $categoryLabel,
        ]).' '.$urgencyText.$blockerText;
    }

    private function script(string $category, string $contact, string $task): string
    {
        $scripts = trans('assessment.tools.money.scripts');
        $template = is_array($scripts) && isset($scripts[$category]) ? $scripts[$category] : __('assessment.tools.money.scripts.overdue');

        return str_replace([':contact', ':task'], [$contact, $task], $template);
    }

    /**
     * @return array<int, string>
     */
    private function steps(string $category): array
    {
        $steps = trans('assessment.tools.money.steps');

        return is_array($steps) && isset($steps[$category]) && is_array($steps[$category])
            ? array_values($steps[$category])
            : [];
    }

    /**
     * @return array<int, string>
     */
    private function questions(string $category, string $outcome): array
    {
        $questions = trans('assessment.tools.money.questions');
        $specific = is_array($questions) && isset($questions[$category]) && is_array($questions[$category]) ? $questions[$category] : [];
        $shared = is_array($questions) && isset($questions['shared']) && is_array($questions['shared']) ? $questions['shared'] : [];

        return [
            ...array_values($specific),
            ...array_values($shared),
            __('assessment.tools.money.outcome_question', ['outcome' => $outcome]),
        ];
    }

    private function defaultTask(string $category): string
    {
        $tasks = trans('assessment.tools.money.defaults.task');

        return is_array($tasks) && isset($tasks[$category]) ? $tasks[$category] : __('assessment.tools.money.defaults.task.overdue');
    }

    private function defaultContact(string $category): string
    {
        $contacts = trans('assessment.tools.money.defaults.contact');

        return is_array($contacts) && isset($contacts[$category]) ? $contacts[$category] : __('assessment.tools.money.defaults.contact.overdue');
    }

    private function category(string $category): string
    {
        return in_array($category, ['bill', 'form', 'message', 'appointment', 'insurance', 'overdue'], true) ? $category : 'overdue';
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
