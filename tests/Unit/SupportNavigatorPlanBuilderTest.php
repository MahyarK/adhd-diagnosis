<?php

namespace Tests\Unit;

use App\Domain\SupportTools\SupportNavigatorInput;
use App\Domain\SupportTools\SupportNavigatorPlanBuilder;
use PHPUnit\Framework\TestCase;

class SupportNavigatorPlanBuilderTest extends TestCase
{
    public function test_it_recommends_tools_for_a_known_problem(): void
    {
        $plan = (new SupportNavigatorPlanBuilder)->build(new SupportNavigatorInput(
            topic: 'money',
            urgency: 'now',
            energy: 'low',
            knownNext: 'open the bill',
        ));

        $this->assertStringContainsString('open the bill', $plan->firstAction);
        $this->assertSame(['planner', 'transition', 'money', 'aid'], $plan->recommendations);
        $this->assertStringContainsString('current problem', $plan->why);
        $this->assertStringContainsString('Stop after choosing one tool', $plan->stopRule);
    }

    public function test_it_uses_safe_defaults_for_unclear_inputs(): void
    {
        $plan = (new SupportNavigatorPlanBuilder)->build(new SupportNavigatorInput(
            topic: 'unknown',
            urgency: 'later',
            energy: 'medium',
            knownNext: '',
        ));

        $this->assertSame(['task', 'body', 'decision'], $plan->recommendations);
        $this->assertStringContainsString('first recommended card', $plan->firstAction);
    }

    public function test_it_includes_wins_log_for_emotional_support(): void
    {
        $plan = (new SupportNavigatorPlanBuilder)->build(new SupportNavigatorInput(
            topic: 'emotional',
            urgency: 'soon',
            energy: 'medium',
            knownNext: '',
        ));

        $this->assertSame(['emotion', 'wins', 'energy', 'support'], $plan->recommendations);
    }
}
