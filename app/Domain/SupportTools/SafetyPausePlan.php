<?php

namespace App\Domain\SupportTools;

final readonly class SafetyPausePlan
{
    /**
     * @param  array<int, string>  $groundingSteps
     * @param  array<int, string>  $nextSteps
     */
    public function __construct(
        public string $immediate,
        public string $firstAction,
        public string $supportScript,
        public array $groundingSteps,
        public array $nextSteps,
        public string $stopRule,
    ) {
    }
}
