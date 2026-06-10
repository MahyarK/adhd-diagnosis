<?php

namespace App\Domain\SupportTools;

final readonly class SupportNavigatorPlan
{
    /**
     * @param  array<int, string>  $recommendations
     */
    public function __construct(
        public string $firstAction,
        public array $recommendations,
        public string $why,
        public string $stopRule,
    ) {
    }
}
