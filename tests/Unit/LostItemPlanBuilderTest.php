<?php

namespace Tests\Unit;

use App\Domain\SupportTools\LostItemInput;
use App\Domain\SupportTools\LostItemPlanBuilder;
use PHPUnit\Framework\TestCase;

class LostItemPlanBuilderTest extends TestCase
{
    public function test_it_builds_a_lost_item_rescue_plan(): void
    {
        $plan = (new LostItemPlanBuilder)->build(new LostItemInput(
            item: 'house keys',
            type: 'keys',
            urgency: 'leave',
            lastSeen: 'kitchen counter',
            searchArea: 'hallway and kitchen',
            landingSpot: 'blue bowl by the door',
        ));

        $this->assertStringContainsString('house keys', $plan->firstAction);
        $this->assertContains('Search hallway and kitchen in one direction for two minutes. Do not tidy unrelated things while searching.', $plan->searchSteps);
        $this->assertStringContainsString('hooks, jacket pockets', implode(' ', $plan->searchSteps));
        $this->assertStringContainsString('five-minute timer', implode(' ', $plan->searchSteps));
        $this->assertStringContainsString('blue bowl by the door', $plan->preventionRule);
        $this->assertStringContainsString('backup steps', $plan->stopRule);
    }

    public function test_it_uses_safe_defaults_for_unclear_inputs(): void
    {
        $plan = (new LostItemPlanBuilder)->build(new LostItemInput(
            item: '',
            type: 'mystery',
            urgency: 'calm',
            lastSeen: '',
            searchArea: '',
            landingSpot: '',
        ));

        $this->assertStringContainsString('the missing item', $plan->firstAction);
        $this->assertStringContainsString('one room or path', $plan->searchSteps[0]);
        $this->assertStringContainsString('one visible landing spot near the door', $plan->preventionRule);
        $this->assertCount(3, $plan->backupSteps);
    }
}
