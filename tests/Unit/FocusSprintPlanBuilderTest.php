<?php

namespace Tests\Unit;

use App\Domain\SupportTools\FocusSprintInput;
use App\Domain\SupportTools\FocusSprintPlanBuilder;
use PHPUnit\Framework\TestCase;

class FocusSprintPlanBuilderTest extends TestCase
{
    public function test_it_builds_a_focus_sprint_from_user_inputs(): void
    {
        $plan = (new FocusSprintPlanBuilder)->build(new FocusSprintInput(
            task: 'answer the scholarship email',
            mode: 'avoidance',
            minutes: '15',
            distraction: 'write it on my sticky note',
            support: 'body double with Sara',
            reward: 'make tea',
        ));

        $this->assertSame('answer the scholarship email', $plan->task);
        $this->assertSame('Set a 15-minute timer and begin: answer the scholarship email.', $plan->startCue);
        $this->assertContains('Do the least scary visible step.', $plan->steps);
        $this->assertStringContainsString('sticky note', $plan->distractionRule);
        $this->assertStringContainsString('Sara', $plan->supportPrompt);
        $this->assertStringContainsString('make tea', $plan->reward);
    }

    public function test_it_uses_defaults_when_the_sprint_is_unclear(): void
    {
        $plan = (new FocusSprintPlanBuilder)->build(new FocusSprintInput(
            task: '',
            mode: 'unknown',
            minutes: '',
            distraction: '',
            support: '',
            reward: '',
        ));

        $this->assertSame('one task', $plan->task);
        $this->assertSame('Set a 10-minute timer and begin: one task.', $plan->startCue);
        $this->assertContains('Open or touch the task.', $plan->steps);
        $this->assertStringContainsString('write it on a parking note', $plan->distractionRule);
        $this->assertStringContainsString('quiet timer', $plan->supportPrompt);
    }
}
