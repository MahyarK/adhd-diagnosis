<?php

namespace App\Domain\SupportTools;

final readonly class LostItemInput
{
    public function __construct(
        public string $item,
        public string $type,
        public string $urgency,
        public string $lastSeen,
        public string $searchArea,
        public string $landingSpot,
    ) {
    }
}
