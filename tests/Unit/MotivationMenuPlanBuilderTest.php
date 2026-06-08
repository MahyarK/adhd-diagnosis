<?php

namespace Tests\Unit;

use App\Domain\SupportTools\MotivationMenuInput;
use App\Domain\SupportTools\MotivationMenuPlanBuilder;
use PHPUnit\Framework\TestCase;

class MotivationMenuPlanBuilderTest extends TestCase
{
    public function test_it_builds_a_motivation_menu_from_user_inputs(): void
    {
        $plan = (new MotivationMenuPlanBuilder)->build(new MotivationMenuInput(
            task: 'start tax form',
            mood: 'bored and avoidant',
            rewardType: 'social',
            stimulation: 'body_double',
            friction: 'I do not know where the document is',
            costLimit: 'free',
        ));

        $this->assertStringContainsString('start tax form', $plan->starter);
        $this->assertContains('Use a body double, focus call, coworking room, or quiet shared space.', $plan->menu);
        $this->assertStringContainsString('another person nearby', $plan->taskPairing);
        $this->assertStringContainsString('I do not know where the document is', $plan->frictionPlan);
        $this->assertContains('Use free, already-owned, or low-risk rewards first.', $plan->rules);
    }

    public function test_it_uses_defaults_when_motivation_is_hard_to_name(): void
    {
        $plan = (new MotivationMenuPlanBuilder)->build(new MotivationMenuInput(
            task: '',
            mood: '',
            rewardType: 'unknown',
            stimulation: 'unknown',
            friction: '',
            costLimit: '',
        ));

        $this->assertStringContainsString('the avoided task', $plan->starter);
        $this->assertContains('Change location, pen, playlist, or browser window before the first step.', $plan->menu);
        $this->assertStringContainsString('music or ambient sound', $plan->taskPairing);
        $this->assertStringContainsString('free or already available', $plan->frictionPlan);
    }
}
