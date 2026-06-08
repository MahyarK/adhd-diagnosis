<?php

namespace Tests\Unit;

use App\Domain\SupportTools\TimeBlockInput;
use App\Domain\SupportTools\TimeBlockPlanBuilder;
use PHPUnit\Framework\TestCase;

class TimeBlockPlanBuilderTest extends TestCase
{
    public function test_it_builds_time_blocks_with_buffers(): void
    {
        $plan = (new TimeBlockPlanBuilder)->build(new TimeBlockInput(
            startTime: '09:00',
            endTime: '12:00',
            mustDo: "Pay bill\nDraft email",
            fixedEvents: "11:00 call",
            bufferLevel: 'heavy',
            energy: 'low',
            recovery: 'eat lunch away from the laptop',
        ));

        $this->assertSame('09:00 - 12:00', $plan->window);
        $this->assertSame('09:00 - 09:20: Pay bill', $plan->blocks[0]);
        $this->assertStringContainsString('25 minutes', $plan->bufferRules[0]);
        $this->assertContains('11:00 call', $plan->fixedAnchors);
        $this->assertStringContainsString('eat lunch away from the laptop', $plan->recoveryBlock);
    }

    public function test_it_uses_defaults_when_time_and_tasks_are_unclear(): void
    {
        $plan = (new TimeBlockPlanBuilder)->build(new TimeBlockInput(
            startTime: '',
            endTime: '',
            mustDo: '',
            fixedEvents: '',
            bufferLevel: 'unknown',
            energy: 'unknown',
            recovery: '',
        ));

        $this->assertSame('09:00 - 17:00', $plan->window);
        $this->assertStringContainsString('choose one must-do item', $plan->blocks[0]);
        $this->assertStringContainsString('15 minutes', $plan->bufferRules[0]);
        $this->assertStringContainsString('No fixed events listed', $plan->fixedAnchors[0]);
        $this->assertStringContainsString('Do not rebuild the whole schedule', $plan->fallbackRule);
    }
}
