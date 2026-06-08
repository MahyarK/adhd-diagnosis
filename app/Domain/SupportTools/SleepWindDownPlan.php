<?php

namespace App\Domain\SupportTools;

final readonly class SleepWindDownPlan
{
    /**
     * @param  array<int, string>  $steps
     * @param  array<int, string>  $rules
     */
    public function __construct(
        public string $firstAction,
        public array $steps,
        public string $screenBoundary,
        public string $tomorrowLaunch,
        public array $rules,
        public string $stopRule,
    ) {
    }
}
