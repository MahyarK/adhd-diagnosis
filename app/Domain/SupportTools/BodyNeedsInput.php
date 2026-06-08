<?php

namespace App\Domain\SupportTools;

final readonly class BodyNeedsInput
{
    public function __construct(
        public string $food,
        public string $water,
        public string $meds,
        public string $sleep,
        public string $bodySignal,
        public string $nextTask,
    ) {
    }
}
