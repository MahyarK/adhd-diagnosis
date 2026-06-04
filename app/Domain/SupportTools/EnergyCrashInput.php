<?php

namespace App\Domain\SupportTools;

final readonly class EnergyCrashInput
{
    public function __construct(
        public string $state,
        public string $must,
        public string $bodyNeed,
        public string $drop,
        public string $support,
        public string $time,
    ) {
    }
}
