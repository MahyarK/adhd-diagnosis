<?php

namespace Tests\Unit;

use App\Domain\SupportTools\EmotionalResetInput;
use App\Domain\SupportTools\EmotionalResetPlanBuilder;
use PHPUnit\Framework\TestCase;

class EmotionalResetPlanBuilderTest extends TestCase
{
    public function test_it_builds_an_emotional_reset_plan_from_user_inputs(): void
    {
        $plan = (new EmotionalResetPlanBuilder)->build(new EmotionalResetInput(
            trigger: 'missing the deadline',
            intensity: 'high',
            bodyState: 'tight',
            story: 'I missed one deadline, not every chance',
            nextAction: 'send one update message',
            support: 'Nora',
        ));

        $this->assertSame('missing the deadline', $plan->trigger);
        $this->assertSame('Loosen one muscle group and exhale longer than you inhale.', $plan->firstReset);
        $this->assertContains('Pause decisions, messages, spending, and self-criticism for ten minutes.', $plan->groundingSteps);
        $this->assertStringContainsString('not a final verdict', $plan->kindReframe);
        $this->assertStringContainsString('send one update message', $plan->nextAction);
        $this->assertStringContainsString('Nora', $plan->supportScript);
    }

    public function test_it_uses_defaults_when_the_spiral_is_hard_to_name(): void
    {
        $plan = (new EmotionalResetPlanBuilder)->build(new EmotionalResetInput(
            trigger: '',
            intensity: 'unknown',
            bodyState: 'unknown',
            story: '',
            nextAction: '',
            support: '',
        ));

        $this->assertSame('this moment', $plan->trigger);
        $this->assertSame('Check the basics: water, food, medication routine, bathroom, rest, or movement.', $plan->firstReset);
        $this->assertContains('Put both feet on the floor and unclench your jaw or shoulders.', $plan->groundingSteps);
        $this->assertStringContainsString('someone safe', $plan->supportScript);
    }
}
