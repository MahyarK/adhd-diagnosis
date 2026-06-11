<?php

namespace App\Domain\SupportTools;

final readonly class SafetyPauseInput
{
    public function __construct(
        public string $state,
        public string $intensity,
        public string $support,
        public string $location,
        public string $nextStep,
        public string $barrier,
    ) {
    }
}
