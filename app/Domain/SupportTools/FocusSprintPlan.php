<?php

namespace App\Domain\SupportTools;

final readonly class FocusSprintPlan
{
    /**
     * @param  array<int, string>  $steps
     */
    public function __construct(
        public string $task,
        public string $startCue,
        public array $steps,
        public string $distractionRule,
        public string $supportPrompt,
        public string $reward,
        public string $stopRule,
    ) {
    }
}
