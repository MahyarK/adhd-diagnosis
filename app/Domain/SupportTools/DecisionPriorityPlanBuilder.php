<?php

namespace App\Domain\SupportTools;

final class DecisionPriorityPlanBuilder
{
    /**
     * @var array<string, string>
     */
    private const REASONS = [
        'deadline' => 'it has the clearest deadline or external consequence',
        'money' => 'it protects money, housing, bills, benefits, or admin safety',
        'health' => 'it protects your body, medication, sleep, food, or care',
        'relationship' => 'it repairs or protects a relationship that matters',
        'relief' => 'it will create the most relief or reduce the most mental noise',
        'unsure' => 'choosing one useful action beats comparing every option again',
    ];

    /**
     * @var array<string, array<int, string>>
     */
    private const STEPS = [
        'low' => [
            'Do only the setup step.',
            'Make the task visible: open the tab, find the object, or write the message title.',
            'Stop after one tiny action if your capacity is still low.',
        ],
        'medium' => [
            'Set a 10-minute timer.',
            'Do the first visible action without improving the whole system.',
            'Write where to restart if you stop.',
        ],
        'high' => [
            'Use the available energy, but keep the scope narrow.',
            'Finish the smallest useful version before adding extras.',
            'Park new ideas on a list instead of switching tasks.',
        ],
    ];

    public function build(DecisionPriorityInput $input): DecisionPriorityPlan
    {
        $options = $this->lines($input->options);
        $chosen = $options[0] ?? 'the first useful option';
        $parked = array_slice($options, 1);
        $energy = array_key_exists($input->energy, self::STEPS) ? $input->energy : 'low';
        $reason = self::REASONS[$input->consequence] ?? self::REASONS['unsure'];
        $support = $this->fallback($input->support, 'someone who can witness the first step');

        if ($input->relief !== '' && $input->relief !== $chosen) {
            $chosen = trim($input->relief);
            $parked = array_values(array_filter($options, fn (string $option): bool => $option !== $chosen));
        }

        return new DecisionPriorityPlan(
            chosenOption: $chosen,
            parkedOptions: $parked,
            whyThis: "Pick this because {$reason}.",
            firstAction: "Start with two minutes on: {$chosen}. No re-deciding during the timer.",
            steps: self::STEPS[$energy],
            supportScript: "Hi {$support}, I am stuck choosing what to do first. I am going to start with {$chosen}. Can you check in or sit with me for the first step?",
            stopRule: 'Stop choosing when one option is good enough to start. The parked list is not deleted; it is just not first.',
        );
    }

    /**
     * @return array<int, string>
     */
    private function lines(string $value): array
    {
        return array_values(array_filter(array_map(
            fn (string $line): string => trim($line),
            preg_split('/\R/', $value) ?: [],
        )));
    }

    private function fallback(string $value, string $fallback): string
    {
        $trimmed = trim($value);

        return $trimmed === '' ? $fallback : $trimmed;
    }
}
