<?php

namespace App\Domain\SupportTools;

final readonly class TimeBlockInput
{
    public function __construct(
        public string $startTime,
        public string $endTime,
        public string $mustDo,
        public string $fixedEvents,
        public string $bufferLevel,
        public string $energy,
        public string $recovery,
    ) {
    }
}
