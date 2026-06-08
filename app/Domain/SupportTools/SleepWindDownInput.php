<?php

namespace App\Domain\SupportTools;

final readonly class SleepWindDownInput
{
    public function __construct(
        public string $mode,
        public string $wakeTime,
        public string $blocker,
        public string $tomorrow,
        public string $screenRule,
        public string $comfort,
    ) {
    }
}
