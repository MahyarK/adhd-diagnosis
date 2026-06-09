<?php

namespace App\Domain\SupportTools;

final readonly class LaundryRescuePlan
{
    /**
     * @param  array<int, string>  $steps
     * @param  array<int, string>  $minimums
     */
    public function __construct(
        public string $firstAction,
        public array $steps,
        public string $dryingPlan,
        public string $emergencyOutfit,
        public array $minimums,
        public string $stopRule,
    ) {
    }
}
