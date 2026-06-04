<?php

namespace Tests\Unit;

use App\Domain\SupportTools\EnergyCrashInput;
use App\Domain\SupportTools\EnergyCrashPlanBuilder;
use PHPUnit\Framework\TestCase;

class EnergyCrashPlanBuilderTest extends TestCase
{
    public function test_it_builds_a_low_capacity_plan_from_user_inputs(): void
    {
        $plan = (new EnergyCrashPlanBuilder)->build(new EnergyCrashInput(
            state: 'overwhelmed',
            must: 'send the rent email',
            bodyNeed: 'food',
            drop: 'laundry',
            support: 'Sam',
            time: '15 minutes',
        ));

        $this->assertSame('For the next 15 minutes, only start: send the rent email. Do not solve the whole day.', $plan->firstAction);
        $this->assertSame('Eat something easy or put food where you can reach it.', $plan->bodyReset);
        $this->assertContains('Circle only the item with the biggest consequence or relief.', $plan->steps);
        $this->assertStringContainsString('laundry', $plan->dropRule);
        $this->assertStringContainsString('Sam', $plan->supportScript);
    }

    public function test_it_uses_safe_defaults_for_unknown_or_empty_inputs(): void
    {
        $plan = (new EnergyCrashPlanBuilder)->build(new EnergyCrashInput(
            state: 'unknown',
            must: '',
            bodyNeed: 'unknown',
            drop: '',
            support: '',
            time: '',
        ));

        $this->assertStringContainsString('one task that matters today', $plan->firstAction);
        $this->assertSame('Drink water or place a full glass next to the task.', $plan->bodyReset);
        $this->assertContains('Lower the target to the smallest version that prevents harm or creates clarity.', $plan->steps);
        $this->assertStringContainsString('anything that is not urgent', $plan->dropRule);
        $this->assertStringContainsString('someone who can make the next step less lonely', $plan->supportScript);
    }
}
