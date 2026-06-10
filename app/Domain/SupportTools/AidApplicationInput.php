<?php

namespace App\Domain\SupportTools;

final readonly class AidApplicationInput
{
    public function __construct(
        public string $program,
        public string $category,
        public string $urgency,
        public string $deadline,
        public string $blocker,
        public string $contact,
        public string $document,
    ) {
    }
}
