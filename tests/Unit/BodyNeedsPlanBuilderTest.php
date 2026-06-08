<?php

namespace Tests\Unit;

use App\Domain\SupportTools\BodyNeedsInput;
use App\Domain\SupportTools\BodyNeedsPlanBuilder;
use PHPUnit\Framework\TestCase;

class BodyNeedsPlanBuilderTest extends TestCase
{
    public function test_it_builds_a_body_needs_plan_from_user_inputs(): void
    {
        $plan = (new BodyNeedsPlanBuilder)->build(new BodyNeedsInput(
            food: 'empty',
            water: 'low',
            meds: 'needed',
            sleep: 'low',
            bodySignal: 'foggy',
            nextTask: 'answer email',
        ));

        $this->assertStringContainsString('Eat the easiest available food', $plan->firstAction);
        $this->assertContains('Drink water now or place it beside the next task.', $plan->supportSteps);
        $this->assertContains('Check your prescribed medication routine or reminder before continuing.', $plan->supportSteps);
        $this->assertStringContainsString('answer email', $plan->taskAdjustment);
        $this->assertStringContainsString('one body need is handled', $plan->stopRule);
    }

    public function test_it_uses_defaults_when_the_user_is_unsure(): void
    {
        $plan = (new BodyNeedsPlanBuilder)->build(new BodyNeedsInput(
            food: '',
            water: '',
            meds: '',
            sleep: '',
            bodySignal: '',
            nextTask: '',
        ));

        $this->assertStringContainsString('Check food first', $plan->firstAction);
        $this->assertContains('If you are unsure, put water in sight and take a few sips.', $plan->supportSteps);
        $this->assertStringContainsString('the next task', $plan->taskAdjustment);
        $this->assertCount(4, $plan->checks);
    }
}
