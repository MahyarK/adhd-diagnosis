<?php

namespace App\Domain\SupportTools;

final readonly class MotivationMenuPlan
{
    /**
     * @param  array<int, string>  $menu
     * @param  array<int, string>  $rules
     */
    public function __construct(
        public string $starter,
        public array $menu,
        public string $taskPairing,
        public string $frictionPlan,
        public array $rules,
    ) {
    }
}
