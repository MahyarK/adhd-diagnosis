<?php

namespace App\Domain\SupportTools;

final readonly class FollowUpRescuePlan
{
    /**
     * @param  array<int, string>  $steps
     * @param  array<int, string>  $questions
     */
    public function __construct(
        public string $firstAction,
        public string $script,
        public array $steps,
        public array $questions,
        public string $stopRule,
    ) {
    }
}
