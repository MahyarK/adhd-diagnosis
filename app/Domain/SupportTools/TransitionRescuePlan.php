<?php

namespace App\Domain\SupportTools;

final readonly class TransitionRescuePlan
{
    /**
     * @param  array<int, string>  $steps
     * @param  array<int, string>  $parkingList
     */
    public function __construct(
        public string $transition,
        public string $firstAction,
        public array $steps,
        public array $parkingList,
        public string $anchorCue,
        public string $supportScript,
        public string $stopRule,
    ) {
    }
}
