<?php

namespace App\Domain\SupportTools;

final readonly class WinsLogPlan
{
    /**
     * @param  array<int, string>  $evidence
     */
    public function __construct(
        public string $reframe,
        public array $evidence,
        public string $repeat,
        public string $shareScript,
        public string $nextAction,
    ) {
    }
}
