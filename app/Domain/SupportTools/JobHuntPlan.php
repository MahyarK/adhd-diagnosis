<?php

namespace App\Domain\SupportTools;

final readonly class JobHuntPlan
{
    /**
     * @param  array<int, string>  $steps
     * @param  array<int, string>  $materials
     */
    public function __construct(
        public string $firstAction,
        public string $script,
        public array $steps,
        public array $materials,
        public string $stopRule,
    ) {
    }
}
