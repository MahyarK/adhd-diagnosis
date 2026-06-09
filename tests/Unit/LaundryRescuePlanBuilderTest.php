<?php

namespace Tests\Unit;

use App\Domain\SupportTools\LaundryRescueInput;
use App\Domain\SupportTools\LaundryRescuePlanBuilder;
use PHPUnit\Framework\TestCase;

class LaundryRescuePlanBuilderTest extends TestCase
{
    public function test_it_builds_a_low_energy_laundry_rescue_plan(): void
    {
        $plan = (new LaundryRescuePlanBuilder)->build(new LaundryRescueInput(
            mode: 'wash',
            needed: 'black work shirt and socks',
            energy: 'low',
            machine: 'shared',
            blocker: 'laundry room feels far away',
            deadline: 'tomorrow morning',
        ));

        $this->assertStringContainsString('black work shirt and socks', $plan->firstAction);
        $this->assertContains('Start one small load instead of collecting every item.', $plan->steps);
        $this->assertContains('If laundry is shared, gather payment/key/detergent before carrying clothes.', $plan->steps);
        $this->assertContains('Low-energy version: one small load, no folding, clean pile allowed.', $plan->steps);
        $this->assertStringContainsString('tomorrow morning', $plan->dryingPlan);
        $this->assertStringContainsString('Re-wearing is allowed', $plan->emergencyOutfit);
    }

    public function test_it_uses_defaults_for_unclear_inputs(): void
    {
        $plan = (new LaundryRescuePlanBuilder)->build(new LaundryRescueInput(
            mode: 'unknown',
            needed: '',
            energy: '',
            machine: '',
            blocker: '',
            deadline: '',
        ));

        $this->assertStringContainsString('one wearable outfit', $plan->firstAction);
        $this->assertContains('Find one wearable outfit or the closest pieces first.', $plan->steps);
        $this->assertStringContainsString('the next time you need clean clothes', $plan->dryingPlan);
        $this->assertCount(3, $plan->minimums);
    }
}
