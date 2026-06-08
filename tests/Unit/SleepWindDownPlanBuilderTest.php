<?php

namespace Tests\Unit;

use App\Domain\SupportTools\SleepWindDownInput;
use App\Domain\SupportTools\SleepWindDownPlanBuilder;
use PHPUnit\Framework\TestCase;

class SleepWindDownPlanBuilderTest extends TestCase
{
    public function test_it_builds_a_revenge_bedtime_plan(): void
    {
        $plan = (new SleepWindDownPlanBuilder)->build(new SleepWindDownInput(
            mode: 'revenge',
            wakeTime: '07:30',
            blocker: 'scrolling because the day felt stolen',
            tomorrow: 'work badge and lunch',
            screenRule: 'charge phone across the room',
            comfort: 'heated blanket',
        ));

        $this->assertStringContainsString('scrolling because the day felt stolen', $plan->firstAction);
        $this->assertStringContainsString('heated blanket', $plan->firstAction);
        $this->assertContains('Name the feeling: “I want time that feels like mine.”', $plan->steps);
        $this->assertStringContainsString('charge phone across the room', $plan->screenBoundary);
        $this->assertStringContainsString('work badge and lunch', $plan->tomorrowLaunch);
        $this->assertCount(3, $plan->rules);
    }

    public function test_it_uses_defaults_for_unclear_inputs(): void
    {
        $plan = (new SleepWindDownPlanBuilder)->build(new SleepWindDownInput(
            mode: 'unknown',
            wakeTime: '',
            blocker: '',
            tomorrow: '',
            screenRule: '',
            comfort: '',
        ));

        $this->assertStringContainsString('the thing keeping you up', $plan->firstAction);
        $this->assertStringContainsString('the time you need to wake up', $plan->tomorrowLaunch);
        $this->assertContains('Move your body toward bed even if your mind is not convinced yet.', $plan->steps);
    }
}
