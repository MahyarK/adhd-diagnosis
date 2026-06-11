<?php

namespace App\Domain\SupportTools;

final readonly class FollowUpRescueInput
{
    public function __construct(
        public string $thing,
        public string $kind,
        public string $lateness,
        public string $person,
        public string $blocker,
        public string $nextDate,
    ) {
    }
}
