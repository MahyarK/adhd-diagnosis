<?php

namespace App\Domain\SupportTools;

final readonly class MedicationRefillPlan
{
    /**
     * @param  array<int, string>  $steps
     * @param  array<int, string>  $questions
     */
    public function __construct(
        public string $firstAction,
        public array $steps,
        public string $script,
        public array $questions,
        public string $safetyNote,
        public string $stopRule,
    ) {
    }
}
