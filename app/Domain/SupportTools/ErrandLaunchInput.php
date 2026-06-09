<?php

namespace App\Domain\SupportTools;

final readonly class ErrandLaunchInput
{
    public function __construct(
        public string $destination,
        public string $kind,
        public string $travel,
        public string $deadline,
        public string $blocker,
        public string $bring,
    ) {
    }
}
