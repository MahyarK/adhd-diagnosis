<?php

namespace Tests\Unit;

use App\Domain\SupportTools\FoodRescueInput;
use App\Domain\SupportTools\FoodRescuePlanBuilder;
use PHPUnit\Framework\TestCase;

class FoodRescuePlanBuilderTest extends TestCase
{
    public function test_it_builds_a_low_energy_food_rescue_plan(): void
    {
        $plan = (new FoodRescuePlanBuilder)->build(new FoodRescueInput(
            energy: 'none',
            appetite: 'forgot',
            kitchen: 'microwave',
            budget: 'very_low',
            available: 'rice, eggs, frozen vegetables',
            nextThing: 'reply to the landlord',
        ));

        $this->assertStringContainsString('body maintenance task', $plan->firstAction);
        $this->assertStringContainsString('microwave', $plan->firstAction);
        $this->assertStringContainsString('rice, eggs, frozen vegetables', $plan->firstAction);
        $this->assertContains('Bread, rice, oats, pasta, or potatoes.', $plan->shoppingList);
        $this->assertStringContainsString('reply to the landlord', $plan->nextCue);
        $this->assertCount(3, $plan->setupRules);
    }

    public function test_it_uses_defaults_for_unclear_inputs(): void
    {
        $plan = (new FoodRescuePlanBuilder)->build(new FoodRescueInput(
            energy: 'unknown',
            appetite: 'unsure',
            kitchen: 'unknown',
            budget: 'unknown',
            available: '',
            nextThing: '',
        ));

        $this->assertStringContainsString('whatever is easiest and safe to eat', $plan->firstAction);
        $this->assertStringContainsString('the next task', $plan->nextCue);
        $this->assertContains('Good enough food counts. This is a rescue, not a performance.', $plan->setupRules);
    }
}
