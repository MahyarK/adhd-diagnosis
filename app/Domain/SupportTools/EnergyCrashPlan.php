<?php

namespace App\Domain\SupportTools;

final readonly class EnergyCrashPlan
{
    /**
     * @param  array<int, string>  $steps
     */
    public function __construct(
        public string $firstAction,
        public string $bodyReset,
        public array $steps,
        public string $dropRule,
        public string $supportScript,
        public string $stopRule,
    ) {
    }
}
