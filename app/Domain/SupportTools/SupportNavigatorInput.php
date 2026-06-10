<?php

namespace App\Domain\SupportTools;

final readonly class SupportNavigatorInput
{
    public function __construct(
        public string $topic,
        public string $urgency,
        public string $energy,
        public string $knownNext,
    ) {
    }
}
