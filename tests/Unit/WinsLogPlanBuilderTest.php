<?php

namespace Tests\Unit;

use App\Domain\SupportTools\WinsLogInput;
use App\Domain\SupportTools\WinsLogPlanBuilder;
use PHPUnit\Framework\TestCase;

class WinsLogPlanBuilderTest extends TestCase
{
    public function test_it_turns_a_small_win_into_evidence(): void
    {
        $plan = (new WinsLogPlanBuilder)->build(new WinsLogInput(
            win: 'opened the form',
            category: 'asked',
            effort: 'hard',
            support: 'body double',
            pattern: 'scripts make calls easier',
            next: 'repeat the first two minutes tomorrow',
        ));

        $this->assertStringContainsString('opened the form', $plan->reframe);
        $this->assertContains('Asking for help counts because support is a strategy, not a character flaw.', $plan->evidence);
        $this->assertContains('This took real effort, so it deserves more credit, not less.', $plan->evidence);
        $this->assertContains('Support that helped: body double.', $plan->evidence);
        $this->assertStringContainsString('body double', $plan->repeat);
        $this->assertStringContainsString('repeat the first two minutes tomorrow', $plan->nextAction);
    }

    public function test_it_uses_safe_defaults_for_empty_inputs(): void
    {
        $plan = (new WinsLogPlanBuilder)->build(new WinsLogInput(
            win: '',
            category: 'unknown',
            effort: 'unknown',
            support: '',
            pattern: '',
            next: '',
        ));

        $this->assertStringContainsString('one small thing moved forward', $plan->reframe);
        $this->assertContains('Starting counts because task initiation is a real executive-function step.', $plan->evidence);
        $this->assertStringContainsString('smallest part that worked', $plan->repeat);
        $this->assertStringContainsString('repeat the easiest part for two minutes', $plan->nextAction);
    }
}
