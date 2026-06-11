<?php

namespace App\Domain\SupportTools;

final readonly class JobHuntInput
{
    public function __construct(
        public string $target,
        public string $stage,
        public string $energy,
        public string $blocker,
        public string $contact,
        public string $deadline,
    ) {
    }
}
