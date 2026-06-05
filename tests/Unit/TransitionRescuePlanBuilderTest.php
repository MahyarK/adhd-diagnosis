<?php

namespace Tests\Unit;

use App\Domain\SupportTools\TransitionRescueInput;
use App\Domain\SupportTools\TransitionRescuePlanBuilder;
use PHPUnit\Framework\TestCase;

class TransitionRescuePlanBuilderTest extends TestCase
{
    public function test_it_builds_a_transition_plan_for_leaving_home(): void
    {
        $plan = (new TransitionRescuePlanBuilder)->build(new TransitionRescueInput(
            transition: 'dentist appointment',
            mode: 'leaving_home',
            time: '14:10',
            anchor: 'keys by the door',
            blocker: 'I keep checking email',
            support: 'Sam',
        ));

        $this->assertSame('dentist appointment', $plan->transition);
        $this->assertSame('Stand up and put the must-have items in one launch spot.', $plan->firstAction);
        $this->assertContains('Leave at the buffer time even if the house or task is unfinished.', $plan->steps);
        $this->assertStringContainsString('I keep checking email', $plan->parkingList[0]);
        $this->assertStringContainsString('Sam', $plan->supportScript);
    }

    public function test_it_uses_unsure_defaults_when_the_transition_is_blurry(): void
    {
        $plan = (new TransitionRescuePlanBuilder)->build(new TransitionRescueInput(
            transition: '',
            mode: 'unexpected',
            time: '',
            anchor: '',
            blocker: '',
            support: '',
        ));

        $this->assertSame('the next transition', $plan->transition);
        $this->assertSame('Set a two-minute timer and choose the next physical place.', $plan->firstAction);
        $this->assertContains('Use a timer so the transition has an edge.', $plan->steps);
        $this->assertStringContainsString('someone safe', $plan->supportScript);
    }
}
