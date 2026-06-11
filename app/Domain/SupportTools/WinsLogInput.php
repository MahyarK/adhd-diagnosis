<?php

namespace App\Domain\SupportTools;

final readonly class WinsLogInput
{
    public function __construct(
        public string $win,
        public string $category,
        public string $effort,
        public string $support,
        public string $pattern,
        public string $next,
    ) {
    }
}
