<?php

namespace App\Domain\SupportTools;

final readonly class DigitalClutterInput
{
    public function __construct(
        public string $mode,
        public string $target,
        public string $energy,
        public string $device,
        public string $blocker,
        public string $deadline,
    ) {
    }
}
