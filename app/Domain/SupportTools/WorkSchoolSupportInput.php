<?php

namespace App\Domain\SupportTools;

final readonly class WorkSchoolSupportInput
{
    public function __construct(
        public string $setting,
        public string $challenge,
        public string $supportStyle,
        public string $friction,
        public string $person,
        public string $trial,
    ) {
    }
}
