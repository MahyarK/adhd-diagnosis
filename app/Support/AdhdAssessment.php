<?php

namespace App\Support;

class AdhdAssessment
{
    public static function sections(): array
    {
        return [
            [
                'key' => 'attention',
                'label' => 'Attention and follow-through',
                'intro' => 'Think about the last six months across school, work, home, relationships, and daily routines.',
                'questions' => [
                    ['id' => 'ia_detail', 'domain' => 'inattentive', 'text' => 'How often do details slip past you, even when you are trying to be careful?'],
                    ['id' => 'ia_sustain', 'domain' => 'inattentive', 'text' => 'How often is it hard to keep attention on reading, conversations, meetings, chores, or tasks?'],
                    ['id' => 'ia_listen', 'domain' => 'inattentive', 'text' => 'How often do people seem to be talking to you while your mind has already moved elsewhere?'],
                    ['id' => 'ia_finish', 'domain' => 'inattentive', 'text' => 'How often do you start something with good intent but struggle to finish the final steps?'],
                    ['id' => 'ia_organize', 'domain' => 'inattentive', 'text' => 'How often does organizing tasks, time, materials, or priorities take much more effort than it seems to take others?'],
                    ['id' => 'ia_avoid', 'domain' => 'inattentive', 'text' => 'How often do you put off tasks that require sustained mental effort, even when they matter to you?'],
                    ['id' => 'ia_lose', 'domain' => 'inattentive', 'text' => 'How often do important items, notes, tabs, tools, or documents go missing in your day-to-day life?'],
                    ['id' => 'ia_distract', 'domain' => 'inattentive', 'text' => 'How often are you pulled off course by thoughts, noises, notifications, or things happening nearby?'],
                    ['id' => 'ia_forget', 'domain' => 'inattentive', 'text' => 'How often do appointments, bills, replies, errands, or routine responsibilities slip your mind?'],
                ],
            ],
            [
                'key' => 'energy',
                'label' => 'Energy, pace, and impulse',
                'intro' => 'These questions are about restlessness and impulsivity. For adults, this may feel internal rather than visibly hyperactive.',
                'questions' => [
                    ['id' => 'hi_fidget', 'domain' => 'hyperactive', 'text' => 'How often do you fidget, tap, shift position, or need something in your hands to stay settled?'],
                    ['id' => 'hi_leave', 'domain' => 'hyperactive', 'text' => 'How often is staying seated difficult when the situation expects it?'],
                    ['id' => 'hi_restless', 'domain' => 'hyperactive', 'text' => 'How often do you feel internally restless, driven, or like your body wants to move?'],
                    ['id' => 'hi_quiet', 'domain' => 'hyperactive', 'text' => 'How often is it hard to relax quietly without reaching for stimulation or another task?'],
                    ['id' => 'hi_on_go', 'domain' => 'hyperactive', 'text' => 'How often do you feel like you are powered by urgency, momentum, or pressure to keep going?'],
                    ['id' => 'hi_talk', 'domain' => 'hyperactive', 'text' => 'How often do you talk more than you meant to, especially when excited, nervous, or trying to connect?'],
                    ['id' => 'hi_blurt', 'domain' => 'hyperactive', 'text' => 'How often do words, decisions, purchases, or messages come out before you have had time to think them through?'],
                    ['id' => 'hi_wait', 'domain' => 'hyperactive', 'text' => 'How often is waiting your turn, standing in lines, or sitting through delays unusually uncomfortable?'],
                    ['id' => 'hi_interrupt', 'domain' => 'hyperactive', 'text' => 'How often do you interrupt, jump in, or take over before noticing you have done it?'],
                ],
            ],
            [
                'key' => 'context',
                'label' => 'Context and impact',
                'intro' => 'ADHD diagnosis depends on pattern, history, settings, impact, and other possible explanations.',
                'questions' => [
                    ['id' => 'age_group', 'kind' => 'choice', 'text' => 'Which age range fits you?', 'options' => ['adult' => '18 or older', 'teen' => '12 to 17', 'child' => 'Under 12']],
                    ['id' => 'childhood', 'kind' => 'choice', 'text' => 'Before age 12, were similar attention, restlessness, or impulsivity patterns present?', 'options' => ['yes' => 'Yes, clearly', 'some' => 'Some signs', 'no' => 'No or unsure']],
                    ['id' => 'settings', 'kind' => 'choice', 'text' => 'Where do these patterns show up?', 'options' => ['many' => 'Several settings', 'two' => 'Two settings', 'one' => 'Mostly one setting']],
                    ['id' => 'impairment', 'kind' => 'choice', 'text' => 'How much do these patterns interfere with life, responsibilities, relationships, or self-trust?', 'options' => ['major' => 'A lot', 'moderate' => 'A meaningful amount', 'minor' => 'A little or rarely']],
                    ['id' => 'duration', 'kind' => 'choice', 'text' => 'How long has this current pattern been noticeable?', 'options' => ['six_months' => 'Six months or longer', 'shorter' => 'Less than six months', 'episodic' => 'Mostly during specific periods']],
                    ['id' => 'sleep', 'kind' => 'choice', 'text' => 'Could sleep loss, irregular sleep, or exhaustion be the main driver right now?', 'options' => ['no' => 'Unlikely', 'possible' => 'Possibly', 'yes' => 'Very possibly']],
                    ['id' => 'mood_anxiety', 'kind' => 'choice', 'text' => 'Could anxiety, depression, grief, or high stress explain most of what is happening right now?', 'options' => ['no' => 'Unlikely', 'possible' => 'Possibly', 'yes' => 'Very possibly']],
                    ['id' => 'substances', 'kind' => 'choice', 'text' => 'Could alcohol, cannabis, stimulants, sedatives, or medication effects be explaining most symptoms?', 'options' => ['no' => 'Unlikely', 'possible' => 'Possibly', 'yes' => 'Very possibly']],
                    ['id' => 'medical', 'kind' => 'choice', 'text' => 'Could a medical issue such as thyroid problems, seizures, concussion, chronic pain, or hormonal changes be a major factor?', 'options' => ['no' => 'Unlikely', 'possible' => 'Possibly', 'yes' => 'Very possibly']],
                    ['id' => 'masking', 'kind' => 'choice', 'text' => 'Do you spend significant energy masking, over-preparing, or building systems so others do not see the struggle?', 'options' => ['yes' => 'Yes, often', 'some' => 'Sometimes', 'no' => 'Not really']],
                    ['id' => 'family', 'kind' => 'choice', 'text' => 'Do ADHD-like patterns run in your family?', 'options' => ['yes' => 'Yes', 'unknown' => 'Not sure', 'no' => 'No']],
                    ['id' => 'support', 'kind' => 'choice', 'text' => 'Would you want a clinician to help you sort this out if the results point that way?', 'options' => ['yes' => 'Yes', 'maybe' => 'Maybe', 'no' => 'Not right now']],
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
            'disclaimer' => 'This is a DSM-5-informed screening aid, not a diagnosis. A qualified clinician can evaluate history, impairment, development, ASRS-style screening results, and other possible causes.',
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
            'combined' => 'Screening pattern: ADHD combined presentation',
            'inattentive' => 'Screening pattern: ADHD predominantly inattentive presentation',
            'hyperactive_impulsive' => 'Screening pattern: ADHD predominantly hyperactive/impulsive presentation',
            'needs_context' => 'Symptoms are present, but diagnosis context is incomplete',
            default => 'ADHD signal is lower on this screening',
        };
    }

    private static function summary(string $type, bool $hasCoreContext, array $ruleOuts): string
    {
        if ($type === 'low_signal') {
            return 'Your answers do not strongly match an ADHD symptom threshold here. That does not mean your struggles are not real; it means this pattern may need a broader look.';
        }

        if (! $hasCoreContext) {
            return 'Your symptom answers show a meaningful ADHD-like pattern, but one or more diagnostic context pieces needs more clarity: early history, multiple settings, impairment, or duration.';
        }

        if ($ruleOuts !== []) {
            return 'Your answers match an ADHD presentation, and they also point to possible overlapping explanations worth checking carefully.';
        }

        return 'Your answers match the symptom pattern and context clinicians typically look for when evaluating ADHD.';
    }

    private static function nextSteps(string $type, array $ruleOuts, array $answers): array
    {
        $steps = [
            'Save or screenshot your results and bring examples from work, school, home, and relationships to a licensed clinician.',
            'Ask someone who knew you before age 12 what they remember about attention, activity level, impulsivity, school reports, or daily routines.',
            'Track sleep, stress, medication/substance use, and symptom intensity for two weeks so the pattern is easier to discuss.',
        ];

        if ($type === 'low_signal') {
            array_unshift($steps, 'Consider screening for anxiety, depression, burnout, sleep problems, trauma, learning differences, or medical causes.');
        }

        if ($ruleOuts !== []) {
            $steps[] = 'Because you marked possible overlapping factors, ask a clinician to rule those in or out rather than assuming one explanation.';
        }

        if (($answers['support'] ?? '') === 'yes') {
            $steps[] = 'A primary care clinician, psychologist, psychiatrist, or ADHD-specialized therapist can be a good starting point.';
        }

        return $steps;
    }
}
