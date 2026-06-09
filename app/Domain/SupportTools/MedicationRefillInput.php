<?php

namespace App\Domain\SupportTools;

final readonly class MedicationRefillInput
{
    public function __construct(
        public string $mode,
        public string $medicine,
        public string $supply,
        public string $blocker,
        public string $contact,
        public string $deadline,
    ) {
    }
}
