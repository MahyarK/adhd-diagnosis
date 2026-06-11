<?php

namespace Tests\Unit;

use App\Domain\SupportTools\JobHuntInput;
use App\Domain\SupportTools\JobHuntPlanBuilder;
use PHPUnit\Framework\TestCase;

class JobHuntPlanBuilderTest extends TestCase
{
    public function test_it_builds_a_practical_job_hunt_plan(): void
    {
        $plan = (new JobHuntPlanBuilder)->build(new JobHuntInput(
            target: 'grocery job application',
            stage: 'application',
            energy: 'urgent',
            blocker: 'too many tabs',
            contact: 'hiring manager',
            deadline: 'Friday morning',
        ));

        $this->assertStringContainsString('grocery job application', $plan->firstAction);
        $this->assertStringContainsString('too many tabs', $plan->firstAction);
        $this->assertStringContainsString('hiring manager', $plan->script);
        $this->assertContains('Open the job post and copy the deadline, title, and link into one note.', $plan->steps);
        $this->assertContains('Save Friday morning as the next job-hunt check-in.', $plan->steps);
        $this->assertContains('Job link or posting', $plan->materials);
        $this->assertStringContainsString('momentum, not proving your worth', $plan->stopRule);
    }

    public function test_it_uses_safe_defaults_for_unclear_job_steps(): void
    {
        $plan = (new JobHuntPlanBuilder)->build(new JobHuntInput(
            target: '',
            stage: 'mystery',
            energy: 'unknown',
            blocker: '',
            contact: '',
            deadline: '',
        ));

        $this->assertStringContainsString('this job or income step', $plan->firstAction);
        $this->assertStringContainsString('the hiring contact, recruiter, support office, or a trusted person', $plan->script);
        $this->assertContains('Choose whether the next useful move is resume, application, interview, follow-up, or income protection.', $plan->steps);
        $this->assertContains('A place to save the restart point', $plan->materials);
    }
}
