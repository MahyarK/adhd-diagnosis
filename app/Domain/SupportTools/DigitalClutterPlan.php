<?php

namespace App\Domain\SupportTools;

final readonly class DigitalClutterPlan
{
    /**
     * @param  array<int, string>  $steps
     * @param  array<int, string>  $parkingRules
     */
    public function __construct(
        public string $firstAction,
        public array $steps,
        public string $searchRule,
        public string $shutdownRule,
        public array $parkingRules,
        public string $stopRule,
    ) {
    }
}
