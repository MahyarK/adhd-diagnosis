<?php

namespace App\Domain\SupportTools;

final readonly class WorkSchoolSupportPlan
{
    /**
     * @param  array<int, string>  $supportOptions
     * @param  array<int, string>  $proofPoints
     */
    public function __construct(
        public string $firstAsk,
        public array $supportOptions,
        public string $script,
        public string $trialPlan,
        public array $proofPoints,
        public string $boundary,
    ) {
    }
}
