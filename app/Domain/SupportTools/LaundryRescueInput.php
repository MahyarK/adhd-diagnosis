<?php

namespace App\Domain\SupportTools;

final readonly class LaundryRescueInput
{
    public function __construct(
        public string $mode,
        public string $needed,
        public string $energy,
        public string $machine,
        public string $blocker,
        public string $deadline,
    ) {
    }
}
