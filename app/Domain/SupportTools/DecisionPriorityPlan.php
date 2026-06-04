<?php

namespace App\Domain\SupportTools;

final readonly class DecisionPriorityPlan
{
    /**
     * @param  array<int, string>  $parkedOptions
     * @param  array<int, string>  $steps
     */
    public function __construct(
        public string $chosenOption,
        public array $parkedOptions,
        public string $whyThis,
        public string $firstAction,
        public array $steps,
        public string $supportScript,
        public string $stopRule,
    ) {
    }
}
