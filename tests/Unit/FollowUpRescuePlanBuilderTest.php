<?php

namespace Tests\Unit;

use App\Domain\SupportTools\FollowUpRescueInput;
use App\Domain\SupportTools\FollowUpRescuePlanBuilder;
use PHPUnit\Framework\TestCase;

class FollowUpRescuePlanBuilderTest extends TestCase
{
    public function test_it_builds_a_practical_follow_up_rescue_plan(): void
    {
        $plan = (new FollowUpRescuePlanBuilder)->build(new FollowUpRescueInput(
            thing: 'clinic referral',
            kind: 'medical',
            lateness: 'week',
            person: 'clinic desk',
            blocker: 'I lost the portal login',
            nextDate: 'Friday morning',
        ));

        $this->assertStringContainsString('clinic referral', $plan->firstAction);
        $this->assertStringContainsString('I lost the portal login', $plan->firstAction);
        $this->assertStringContainsString('clinic desk', $plan->script);
        $this->assertStringContainsString('clinic referral', $plan->script);
        $this->assertContains('Find the last portal message, referral, prescription, or appointment note.', $plan->steps);
        $this->assertContains('Save Friday morning as the next follow-up point.', $plan->steps);
        $this->assertContains('What is the current status?', $plan->questions);
        $this->assertStringContainsString('Stop after one status check', $plan->stopRule);
    }

    public function test_it_uses_safe_defaults_when_the_follow_up_is_unclear(): void
    {
        $plan = (new FollowUpRescuePlanBuilder)->build(new FollowUpRescueInput(
            thing: '',
            kind: 'mystery',
            lateness: 'unknown',
            person: '',
            blocker: '',
            nextDate: '',
        ));

        $this->assertStringContainsString('this follow-up', $plan->firstAction);
        $this->assertStringContainsString('the right person or office', $plan->script);
        $this->assertContains('Find the last place this existed: inbox, portal, notes, calendar, or paper pile.', $plan->steps);
        $this->assertContains('Save the next check-in date as the next follow-up point.', $plan->steps);
        $this->assertContains('Who can tell me the current status?', $plan->questions);
    }
}
