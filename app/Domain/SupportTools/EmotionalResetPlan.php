<?php

namespace App\Domain\SupportTools;

final readonly class EmotionalResetPlan
{
    /**
     * @param  array<int, string>  $groundingSteps
     */
    public function __construct(
        public string $trigger,
        public string $firstReset,
        public array $groundingSteps,
        public string $kindReframe,
        public string $nextAction,
        public string $supportScript,
        public string $stopRule,
    ) {
    }
}
