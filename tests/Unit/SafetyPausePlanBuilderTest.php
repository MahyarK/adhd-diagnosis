<?php

namespace Tests\Unit;

use App\Domain\SupportTools\SafetyPauseInput;
use App\Domain\SupportTools\SafetyPausePlanBuilder;
use PHPUnit\Framework\TestCase;

class SafetyPausePlanBuilderTest extends TestCase
{
    public function test_it_escalates_uncertain_safety_to_live_support(): void
    {
        $plan = (new SafetyPausePlanBuilder)->build(new SafetyPauseInput(
            state: 'unsure',
            intensity: 'high',
            support: 'Sam',
            location: 'the hallway',
            nextStep: 'call Sam',
            barrier: 'I feel embarrassed',
        ));

        $this->assertStringContainsString('call or text 988', $plan->immediate);
        $this->assertStringContainsString('the hallway', $plan->firstAction);
        $this->assertStringContainsString('I feel embarrassed', $plan->firstAction);
        $this->assertStringContainsString('Sam', $plan->supportScript);
        $this->assertContains('Move away from anything you could use to hurt yourself.', $plan->groundingSteps);
        $this->assertContains('Do not stay alone with uncertainty if safety might drop.', $plan->nextSteps);
        $this->assertStringContainsString('switch to live help', $plan->stopRule);
    }

    public function test_it_builds_a_stabilizing_plan_when_safe_but_overwhelmed(): void
    {
        $plan = (new SafetyPausePlanBuilder)->build(new SafetyPauseInput(
            state: 'safe',
            intensity: 'low',
            support: '',
            location: '',
            nextStep: '',
            barrier: '',
        ));

        $this->assertStringContainsString('not-immediate-danger', $plan->immediate);
        $this->assertStringContainsString('where I am', $plan->firstAction);
        $this->assertStringContainsString('someone who can respond now', $plan->supportScript);
        $this->assertContains('Put both feet on the floor and name one thing you can see.', $plan->groundingSteps);
        $this->assertContains('Do one body support action: water, food, medication routine, bathroom, warmth, cooling, or rest.', $plan->nextSteps);
    }
}
