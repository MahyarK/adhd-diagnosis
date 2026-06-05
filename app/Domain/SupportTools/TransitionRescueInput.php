<?php

namespace App\Domain\SupportTools;

final readonly class TransitionRescueInput
{
    public function __construct(
        public string $transition,
        public string $mode,
        public string $time,
        public string $anchor,
        public string $blocker,
        public string $support,
    ) {
    }
}
