<?php

namespace Tests\Unit;

use App\Domain\SupportTools\DecisionPriorityInput;
use App\Domain\SupportTools\DecisionPriorityPlanBuilder;
use PHPUnit\Framework\TestCase;

class DecisionPriorityPlanBuilderTest extends TestCase
{
    public function test_it_prioritizes_the_first_option_and_parks_the_rest(): void
    {
        $plan = (new DecisionPriorityPlanBuilder)->build(new DecisionPriorityInput(
            options: "pay rent\nreply to friend\nclean desk",
            urgency: 'today',
            energy: 'medium',
            consequence: 'money',
            relief: '',
            support: 'Mina',
        ));

        $this->assertSame('pay rent', $plan->chosenOption);
        $this->assertSame(['reply to friend', 'clean desk'], $plan->parkedOptions);
        $this->assertSame('Pick this because it protects money, housing, bills, benefits, or admin safety.', $plan->whyThis);
        $this->assertContains('Set a 10-minute timer.', $plan->steps);
        $this->assertStringContainsString('Mina', $plan->supportScript);
    }

    public function test_it_can_prioritize_the_biggest_relief_option(): void
    {
        $plan = (new DecisionPriorityPlanBuilder)->build(new DecisionPriorityInput(
            options: "laundry\nbook appointment\nemail teacher",
            urgency: 'unsure',
            energy: 'low',
            consequence: 'relief',
            relief: 'email teacher',
            support: '',
        ));

        $this->assertSame('email teacher', $plan->chosenOption);
        $this->assertSame(['laundry', 'book appointment'], $plan->parkedOptions);
        $this->assertContains('Do only the setup step.', $plan->steps);
        $this->assertStringContainsString('someone who can witness the first step', $plan->supportScript);
    }
}
