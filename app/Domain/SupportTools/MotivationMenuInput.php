<?php

namespace App\Domain\SupportTools;

final readonly class MotivationMenuInput
{
    public function __construct(
        public string $task,
        public string $mood,
        public string $rewardType,
        public string $stimulation,
        public string $friction,
        public string $costLimit,
    ) {
    }
}
