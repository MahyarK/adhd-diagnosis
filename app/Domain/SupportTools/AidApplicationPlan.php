<?php

namespace App\Domain\SupportTools;

final readonly class AidApplicationPlan
{
    /**
     * @param  array<int, string>  $documents
     * @param  array<int, string>  $questions
     * @param  array<int, string>  $statusSteps
     */
    public function __construct(
        public string $firstAction,
        public array $documents,
        public string $script,
        public array $questions,
        public array $statusSteps,
        public string $stopRule,
    ) {
    }
}
