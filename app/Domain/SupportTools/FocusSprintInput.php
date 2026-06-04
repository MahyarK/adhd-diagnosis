<?php

namespace App\Domain\SupportTools;

final readonly class FocusSprintInput
{
    public function __construct(
        public string $task,
        public string $mode,
        public string $minutes,
        public string $distraction,
        public string $support,
        public string $reward,
    ) {
    }
}
