<?php

namespace App\Domain\SupportTools;

final readonly class FoodRescuePlan
{
    /**
     * @param  array<int, string>  $options
     * @param  array<int, string>  $shoppingList
     * @param  array<int, string>  $setupRules
     */
    public function __construct(
        public string $firstAction,
        public array $options,
        public array $shoppingList,
        public string $nextCue,
        public array $setupRules,
        public string $stopRule,
    ) {
    }
}
