<?php

namespace App\Domain\SupportTools;

final class WinsLogPlanBuilder
{
    /**
     * @var array<string, string>
     */
    private const CATEGORY_REFRAMES = [
        'started' => 'Starting counts because task initiation is a real executive-function step.',
        'finished' => 'Finishing counts, even if the path was messy or later than planned.',
        'asked' => 'Asking for help counts because support is a strategy, not a character flaw.',
        'recovered' => 'Restarting after drift counts because recovery is part of ADHD life.',
        'rested' => 'Resting before damage counts because protecting capacity is productive.',
        'learned' => 'Noticing a pattern counts because it makes tomorrow easier to design.',
    ];

    public function build(WinsLogInput $input): WinsLogPlan
    {
        $category = array_key_exists($input->category, self::CATEGORY_REFRAMES) ? $input->category : 'started';
        $win = trim($input->win) !== '' ? trim($input->win) : 'one small thing moved forward';
        $support = trim($input->support);
        $pattern = trim($input->pattern);
        $next = trim($input->next) !== '' ? trim($input->next) : 'repeat the easiest part for two minutes';

        $evidence = [
            "Win: {$win}.",
            self::CATEGORY_REFRAMES[$category],
            $this->effortLine($input->effort),
        ];

        if ($support !== '') {
            $evidence[] = "Support that helped: {$support}.";
        }

        if ($pattern !== '') {
            $evidence[] = "Pattern noticed: {$pattern}.";
        }

        return new WinsLogPlan(
            reframe: "This is evidence, not luck: {$win}.",
            evidence: $evidence,
            repeat: $support !== ''
                ? "Make the next attempt easier by reusing {$support}."
                : 'Make the next attempt easier by copying the smallest part that worked.',
            shareScript: "I am tracking what helps my ADHD. A small win was: {$win}. The support or pattern worth remembering is: ".($support !== '' ? $support : ($pattern !== '' ? $pattern : 'starting smaller helped')).'.',
            nextAction: "Next tiny repeat: {$next}.",
        );
    }

    private function effortLine(string $effort): string
    {
        return match ($effort) {
            'hard' => 'This took real effort, so it deserves more credit, not less.',
            'medium' => 'This used some effort and gives a clue about what support works.',
            'low' => 'Low-friction wins are valuable because they are easier to repeat.',
            default => 'Any amount of effort can count when the action supports your life.',
        };
    }
}
