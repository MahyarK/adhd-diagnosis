<?php

namespace App\Domain\SupportTools;

final readonly class TimeBlockPlan
{
    /**
     * @param  array<int, string>  $blocks
     * @param  array<int, string>  $bufferRules
     * @param  array<int, string>  $fixedAnchors
     */
    public function __construct(
        public string $window,
        public array $blocks,
        public array $bufferRules,
        public array $fixedAnchors,
        public string $recoveryBlock,
        public string $fallbackRule,
    ) {
    }
}
