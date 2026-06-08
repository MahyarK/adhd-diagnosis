<?php

namespace App\Domain\SupportTools;

final readonly class AccountabilityPlan
{
    /**
     * @param  array<int, string>  $steps
     * @param  array<int, string>  $rules
     */
    public function __construct(
        public string $script,
        public array $steps,
        public string $proof,
        public string $missedPlan,
        public array $rules,
    ) {
    }
}
