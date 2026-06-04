<?php

namespace App\Domain\SupportTools;

final readonly class EmotionalResetInput
{
    public function __construct(
        public string $trigger,
        public string $intensity,
        public string $bodyState,
        public string $story,
        public string $nextAction,
        public string $support,
    ) {
    }
}
