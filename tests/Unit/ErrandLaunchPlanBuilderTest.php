<?php

namespace Tests\Unit;

use App\Domain\SupportTools\ErrandLaunchInput;
use App\Domain\SupportTools\ErrandLaunchPlanBuilder;
use PHPUnit\Framework\TestCase;

class ErrandLaunchPlanBuilderTest extends TestCase
{
    public function test_it_builds_an_errand_launch_plan(): void
    {
        $plan = (new ErrandLaunchPlanBuilder)->build(new ErrandLaunchInput(
            destination: 'pharmacy pickup',
            kind: 'pharmacy_store',
            travel: 'public_transport',
            deadline: 'leave at 14:10',
            blocker: 'I keep adding extra shopping',
            bring: "ID\ninsurance card\nmedication bottle",
        ));

        $this->assertStringContainsString('pharmacy pickup', $plan->firstAction);
        $this->assertContains('Set one alarm for leave at 14:10 and one backup alarm five minutes earlier.', $plan->steps);
        $this->assertStringContainsString('next departure', implode(' ', $plan->steps));
        $this->assertStringContainsString('pickup code', implode(' ', $plan->steps));
        $this->assertStringContainsString('I keep adding extra shopping', implode(' ', $plan->steps));
        $this->assertSame(['ID', 'insurance card', 'medication bottle'], $plan->bringList);
        $this->assertStringContainsString('may be late', $plan->lateScript);
        $this->assertCount(3, $plan->backupPlan);
    }

    public function test_it_uses_safe_defaults_for_unclear_inputs(): void
    {
        $plan = (new ErrandLaunchPlanBuilder)->build(new ErrandLaunchInput(
            destination: '',
            kind: 'mystery',
            travel: 'mystery',
            deadline: '',
            blocker: '',
            bring: '',
        ));

        $this->assertStringContainsString('the errand', $plan->firstAction);
        $this->assertStringContainsString('the time you need to leave or start', $plan->steps[0]);
        $this->assertStringContainsString('lowest-friction route', implode(' ', $plan->steps));
        $this->assertStringContainsString('next step', implode(' ', $plan->steps));
        $this->assertContains('Phone', $plan->bringList);
        $this->assertStringContainsString('essentials are gathered', $plan->stopRule);
    }
}
