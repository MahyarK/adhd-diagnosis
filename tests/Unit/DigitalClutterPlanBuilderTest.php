<?php

namespace Tests\Unit;

use App\Domain\SupportTools\DigitalClutterInput;
use App\Domain\SupportTools\DigitalClutterPlanBuilder;
use PHPUnit\Framework\TestCase;

class DigitalClutterPlanBuilderTest extends TestCase
{
    public function test_it_builds_a_low_energy_tab_rescue_plan(): void
    {
        $plan = (new DigitalClutterPlanBuilder)->build(new DigitalClutterInput(
            mode: 'tabs',
            target: 'insurance form link',
            energy: 'low',
            device: 'computer',
            blocker: 'too many open tabs',
            deadline: 'before the appointment',
        ));

        $this->assertStringContainsString('insurance form link', $plan->firstAction);
        $this->assertContains('Keep only the tab needed for the current target visible.', $plan->steps);
        $this->assertContains('On computer: use browser history, downloads, recent files, or app search before manually browsing folders.', $plan->steps);
        $this->assertContains('Low-energy version: rescue one target only, then stop.', $plan->steps);
        $this->assertStringContainsString('before the appointment', $plan->shutdownRule);
        $this->assertStringContainsString('not an inbox-cleaning marathon', $plan->stopRule);
    }

    public function test_it_uses_defaults_for_unclear_inputs(): void
    {
        $plan = (new DigitalClutterPlanBuilder)->build(new DigitalClutterInput(
            mode: '',
            target: '',
            energy: '',
            device: '',
            blocker: '',
            deadline: '',
        ));

        $this->assertStringContainsString('one digital next step', $plan->firstAction);
        $this->assertContains('Choose one digital surface: tabs, inbox, files, or phone home screen.', $plan->steps);
        $this->assertStringContainsString('today', $plan->shutdownRule);
        $this->assertCount(3, $plan->parkingRules);
    }
}
