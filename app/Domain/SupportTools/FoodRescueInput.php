<?php

namespace App\Domain\SupportTools;

final readonly class FoodRescueInput
{
    public function __construct(
        public string $energy,
        public string $appetite,
        public string $kitchen,
        public string $budget,
        public string $available,
        public string $nextThing,
    ) {
    }
}
