<?php

namespace App\Domain\SupportTools;

final readonly class LostItemPlan
{
    /**
     * @param  array<int, string>  $searchSteps
     * @param  array<int, string>  $backupSteps
     */
    public function __construct(
        public string $firstAction,
        public array $searchSteps,
        public array $backupSteps,
        public string $preventionRule,
        public string $stopRule,
    ) {
    }
}
