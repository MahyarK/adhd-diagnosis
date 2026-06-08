<?php

namespace Tests\Unit;

use App\Domain\SupportTools\AccountabilityInput;
use App\Domain\SupportTools\AccountabilityPlanBuilder;
use PHPUnit\Framework\TestCase;

class AccountabilityPlanBuilderTest extends TestCase
{
    public function test_it_builds_an_accountability_check_in_plan(): void
    {
        $plan = (new AccountabilityPlanBuilder)->build(new AccountabilityInput(
            task: 'start essay outline',
            person: 'Mina',
            format: 'body_double',
            time: '18:30',
            proof: 'send a screenshot of the first three bullets',
            missed: 'text "reset" and start with the title only',
        ));

        $this->assertStringContainsString('Mina', $plan->script);
        $this->assertStringContainsString('focus call', $plan->script);
        $this->assertContains('At 18:30: start start essay outline for two minutes while Mina witnesses or checks in.', $plan->steps);
        $this->assertStringContainsString('first three bullets', $plan->proof);
        $this->assertStringContainsString('No shame recap', $plan->missedPlan);
    }

    public function test_it_uses_defaults_for_unclear_requests(): void
    {
        $plan = (new AccountabilityPlanBuilder)->build(new AccountabilityInput(
            task: '',
            person: '',
            format: 'unknown',
            time: '',
            proof: '',
            missed: '',
        ));

        $this->assertStringContainsString('someone safe', $plan->script);
        $this->assertStringContainsString('send a quick text check-in', $plan->script);
        $this->assertStringContainsString('one photo, screenshot, checkmark, or sentence', $plan->proof);
        $this->assertCount(3, $plan->rules);
    }
}
