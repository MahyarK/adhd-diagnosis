<?php

namespace App\Domain\SupportTools;

final readonly class DecisionPriorityInput
{
    public function __construct(
        public string $options,
        public string $urgency,
        public string $energy,
        public string $consequence,
        public string $relief,
        public string $support,
    ) {
    }
}
