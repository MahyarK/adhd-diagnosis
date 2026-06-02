<?php

namespace App\Support;

class AdhdAssessment
{
    public const SUPPORTED_LOCALES = ['en', 'nl', 'fr', 'fa'];

    public static function sections(): array
    {
        return [
            [
                'key' => 'attention',
                'label' => __('assessment.sections.attention.label'),
                'intro' => __('assessment.sections.attention.intro'),
                'questions' => [
                    ['id' => 'ia_detail', 'domain' => 'inattentive', 'text' => __('assessment.questions.ia_detail')],
                    ['id' => 'ia_sustain', 'domain' => 'inattentive', 'text' => __('assessment.questions.ia_sustain')],
                    ['id' => 'ia_listen', 'domain' => 'inattentive', 'text' => __('assessment.questions.ia_listen')],
                    ['id' => 'ia_finish', 'domain' => 'inattentive', 'text' => __('assessment.questions.ia_finish')],
                    ['id' => 'ia_organize', 'domain' => 'inattentive', 'text' => __('assessment.questions.ia_organize')],
                    ['id' => 'ia_avoid', 'domain' => 'inattentive', 'text' => __('assessment.questions.ia_avoid')],
                    ['id' => 'ia_lose', 'domain' => 'inattentive', 'text' => __('assessment.questions.ia_lose')],
                    ['id' => 'ia_distract', 'domain' => 'inattentive', 'text' => __('assessment.questions.ia_distract')],
                    ['id' => 'ia_forget', 'domain' => 'inattentive', 'text' => __('assessment.questions.ia_forget')],
                ],
            ],
            [
                'key' => 'energy',
                'label' => __('assessment.sections.energy.label'),
                'intro' => __('assessment.sections.energy.intro'),
                'questions' => [
                    ['id' => 'hi_fidget', 'domain' => 'hyperactive', 'text' => __('assessment.questions.hi_fidget')],
                    ['id' => 'hi_leave', 'domain' => 'hyperactive', 'text' => __('assessment.questions.hi_leave')],
                    ['id' => 'hi_restless', 'domain' => 'hyperactive', 'text' => __('assessment.questions.hi_restless')],
                    ['id' => 'hi_quiet', 'domain' => 'hyperactive', 'text' => __('assessment.questions.hi_quiet')],
                    ['id' => 'hi_on_go', 'domain' => 'hyperactive', 'text' => __('assessment.questions.hi_on_go')],
                    ['id' => 'hi_talk', 'domain' => 'hyperactive', 'text' => __('assessment.questions.hi_talk')],
                    ['id' => 'hi_blurt', 'domain' => 'hyperactive', 'text' => __('assessment.questions.hi_blurt')],
                    ['id' => 'hi_wait', 'domain' => 'hyperactive', 'text' => __('assessment.questions.hi_wait')],
                    ['id' => 'hi_interrupt', 'domain' => 'hyperactive', 'text' => __('assessment.questions.hi_interrupt')],
                ],
            ],
            [
                'key' => 'context',
                'label' => __('assessment.sections.context.label'),
                'intro' => __('assessment.sections.context.intro'),
                'questions' => [
                    ['id' => 'age_group', 'kind' => 'choice', 'text' => __('assessment.questions.age_group'), 'options' => __('assessment.options.age_group')],
                    ['id' => 'childhood', 'kind' => 'choice', 'text' => __('assessment.questions.childhood'), 'options' => __('assessment.options.childhood')],
                    ['id' => 'settings', 'kind' => 'choice', 'text' => __('assessment.questions.settings'), 'options' => __('assessment.options.settings')],
                    ['id' => 'impairment', 'kind' => 'choice', 'text' => __('assessment.questions.impairment'), 'options' => __('assessment.options.impairment')],
                    ['id' => 'duration', 'kind' => 'choice', 'text' => __('assessment.questions.duration'), 'options' => __('assessment.options.duration')],
                    ['id' => 'sleep', 'kind' => 'choice', 'text' => __('assessment.questions.sleep'), 'options' => __('assessment.options.rule_out')],
                    ['id' => 'mood_anxiety', 'kind' => 'choice', 'text' => __('assessment.questions.mood_anxiety'), 'options' => __('assessment.options.rule_out')],
                    ['id' => 'substances', 'kind' => 'choice', 'text' => __('assessment.questions.substances'), 'options' => __('assessment.options.rule_out')],
                    ['id' => 'medical', 'kind' => 'choice', 'text' => __('assessment.questions.medical'), 'options' => __('assessment.options.rule_out')],
                    ['id' => 'masking', 'kind' => 'choice', 'text' => __('assessment.questions.masking'), 'options' => __('assessment.options.masking')],
                    ['id' => 'family', 'kind' => 'choice', 'text' => __('assessment.questions.family'), 'options' => __('assessment.options.family')],
                    ['id' => 'support', 'kind' => 'choice', 'text' => __('assessment.questions.support'), 'options' => __('assessment.options.support')],
                ],
            ],
        ];
    }

    public static function score(array $answers): array
    {
        $domains = ['inattentive' => [], 'hyperactive' => []];

        foreach (self::sections() as $section) {
            foreach ($section['questions'] as $question) {
                if (! isset($question['domain'])) {
                    continue;
                }

                $domains[$question['domain']][] = (int) ($answers[$question['id']] ?? 0);
            }
        }

        $counts = [
            'inattentive' => count(array_filter($domains['inattentive'], fn (int $score): bool => $score >= 2)),
            'hyperactive' => count(array_filter($domains['hyperactive'], fn (int $score): bool => $score >= 2)),
        ];

        $age = $answers['age_group'] ?? 'adult';
        $threshold = $age === 'adult' ? 5 : 6;
        $meets = [
            'inattentive' => $counts['inattentive'] >= $threshold,
            'hyperactive' => $counts['hyperactive'] >= $threshold,
        ];

        $context = [
            'childhood' => in_array($answers['childhood'] ?? '', ['yes', 'some'], true),
            'settings' => in_array($answers['settings'] ?? '', ['many', 'two'], true),
            'impairment' => in_array($answers['impairment'] ?? '', ['major', 'moderate'], true),
            'duration' => ($answers['duration'] ?? '') === 'six_months',
        ];

        $ruleOuts = collect(['sleep', 'mood_anxiety', 'substances', 'medical'])
            ->filter(fn (string $key): bool => in_array($answers[$key] ?? '', ['possible', 'yes'], true))
            ->values()
            ->all();

        $hasCoreContext = $context['childhood'] && $context['settings'] && $context['impairment'] && $context['duration'];
        $type = self::type($meets, $hasCoreContext);

        return [
            'type' => $type,
            'title' => self::title($type),
            'summary' => self::summary($type, $hasCoreContext, $ruleOuts),
            'counts' => $counts,
            'threshold' => $threshold,
            'context' => $context,
            'rule_outs' => $ruleOuts,
            'next_steps' => self::nextSteps($type, $ruleOuts, $answers),
            'profile' => self::profile($type, $ruleOuts),
            'disclaimer' => __('assessment.results.disclaimer'),
        ];
    }

    private static function type(array $meets, bool $hasCoreContext): string
    {
        if (! $hasCoreContext) {
            return $meets['inattentive'] || $meets['hyperactive'] ? 'needs_context' : 'low_signal';
        }

        return match (true) {
            $meets['inattentive'] && $meets['hyperactive'] => 'combined',
            $meets['inattentive'] => 'inattentive',
            $meets['hyperactive'] => 'hyperactive_impulsive',
            default => 'low_signal',
        };
    }

    private static function title(string $type): string
    {
        return match ($type) {
            'combined' => __('assessment.results.titles.combined'),
            'inattentive' => __('assessment.results.titles.inattentive'),
            'hyperactive_impulsive' => __('assessment.results.titles.hyperactive_impulsive'),
            'needs_context' => __('assessment.results.titles.needs_context'),
            default => __('assessment.results.titles.low_signal'),
        };
    }

    private static function summary(string $type, bool $hasCoreContext, array $ruleOuts): string
    {
        if ($type === 'low_signal') {
            return __('assessment.results.summaries.low_signal');
        }

        if (! $hasCoreContext) {
            return __('assessment.results.summaries.needs_context');
        }

        if ($ruleOuts !== []) {
            return __('assessment.results.summaries.with_rule_outs');
        }

        return __('assessment.results.summaries.match');
    }

    private static function nextSteps(string $type, array $ruleOuts, array $answers): array
    {
        $steps = [
            __('assessment.results.next_steps.save'),
            __('assessment.results.next_steps.childhood'),
            __('assessment.results.next_steps.track'),
        ];

        if ($type === 'low_signal') {
            array_unshift($steps, __('assessment.results.next_steps.broader'));
        }

        if ($ruleOuts !== []) {
            $steps[] = __('assessment.results.next_steps.rule_outs');
        }

        if (($answers['support'] ?? '') === 'yes') {
            $steps[] = __('assessment.results.next_steps.clinician');
        }

        return $steps;
    }

    private static function profile(string $type, array $ruleOuts): array
    {
        $profile = self::translated('assessment.results.profiles.'.$type);

        if (! is_array($profile)) {
            $profile = self::translated('assessment.results.profiles.low_signal');
        }

        if ($ruleOuts !== []) {
            $profile['important'][] = self::translated('assessment.results.extra.rule_out_note');
        }

        return $profile;
    }

    private static function translated(string $key): mixed
    {
        $value = trans($key);

        if ($value !== $key) {
            return $value;
        }

        return trans($key, [], 'en');
    }
}
