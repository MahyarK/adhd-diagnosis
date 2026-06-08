<?php

namespace App\Domain\SupportTools;

final readonly class AccountabilityInput
{
    public function __construct(
        public string $task,
        public string $person,
        public string $format,
        public string $time,
        public string $proof,
        public string $missed,
    ) {
    }
}
