<?php

namespace App\Domain\SupportTools;

final readonly class BodyNeedsPlan
{
    /**
     * @param  array<int, string>  $supportSteps
     * @param  array<int, string>  $checks
     */
    public function __construct(
        public string $firstAction,
        public array $supportSteps,
        public array $checks,
        public string $taskAdjustment,
        public string $stopRule,
    ) {
    }
}
