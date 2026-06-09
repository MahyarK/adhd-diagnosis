<?php

namespace App\Domain\SupportTools;

final readonly class ErrandLaunchPlan
{
    /**
     * @param  array<int, string>  $steps
     * @param  array<int, string>  $bringList
     * @param  array<int, string>  $backupPlan
     */
    public function __construct(
        public string $firstAction,
        public array $steps,
        public array $bringList,
        public string $lateScript,
        public array $backupPlan,
        public string $stopRule,
    ) {
    }
}
