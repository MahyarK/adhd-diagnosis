<?php

namespace Tests\Unit;

use App\Domain\SupportTools\WorkSchoolSupportInput;
use App\Domain\SupportTools\WorkSchoolSupportPlanBuilder;
use PHPUnit\Framework\TestCase;

class WorkSchoolSupportPlanBuilderTest extends TestCase
{
    public function test_it_builds_a_work_support_plan(): void
    {
        $plan = (new WorkSchoolSupportPlanBuilder)->build(new WorkSchoolSupportInput(
            setting: 'work',
            challenge: 'deadlines',
            supportStyle: 'checkin',
            friction: 'deadlines sneak up until everything is urgent',
            person: 'project lead',
            trial: 'one project',
        ));

        $this->assertStringContainsString('one project', $plan->firstAsk);
        $this->assertStringContainsString('check-in', implode(' ', $plan->supportOptions));
        $this->assertStringContainsString('midpoint check-in', implode(' ', $plan->supportOptions));
        $this->assertStringContainsString('project lead', $plan->script);
        $this->assertStringContainsString('deadlines sneak up', $plan->script);
        $this->assertCount(3, $plan->proofPoints);
        $this->assertStringContainsString('not legal advice', $plan->boundary);
    }

    public function test_it_uses_safe_defaults_for_unclear_inputs(): void
    {
        $plan = (new WorkSchoolSupportPlanBuilder)->build(new WorkSchoolSupportInput(
            setting: 'unknown',
            challenge: 'unknown',
            supportStyle: 'unknown',
            friction: '',
            person: '',
            trial: '',
        ));

        $this->assertStringContainsString('work support', $plan->firstAsk);
        $this->assertStringContainsString('working memory', implode(' ', $plan->supportOptions));
        $this->assertStringContainsString('Written priorities', implode(' ', $plan->supportOptions));
        $this->assertStringContainsString('manager, HR, or trusted lead', $plan->script);
        $this->assertStringContainsString('two weeks', $plan->trialPlan);
    }
}
