<?php

namespace Tests\Unit;

use App\Domain\SupportTools\AidApplicationInput;
use App\Domain\SupportTools\AidApplicationPlanBuilder;
use PHPUnit\Framework\TestCase;

class AidApplicationPlanBuilderTest extends TestCase
{
    public function test_it_builds_a_practical_aid_application_plan(): void
    {
        $plan = (new AidApplicationPlanBuilder)->build(new AidApplicationInput(
            program: 'clinic charity care',
            category: 'medical',
            urgency: 'today',
            deadline: 'Friday',
            blocker: 'missing income proof',
            contact: 'billing office',
            document: 'latest payslip',
        ));

        $this->assertStringContainsString('clinic charity care', $plan->firstAction);
        $this->assertStringContainsString('missing income proof', $plan->firstAction);
        $this->assertSame('latest payslip', $plan->documents[0]);
        $this->assertStringContainsString('billing office', $plan->script);
        $this->assertStringContainsString('Friday', $plan->script);
        $this->assertContains('Is charity care, sliding scale, or a payment plan available?', $plan->questions);
        $this->assertCount(4, $plan->statusSteps);
    }

    public function test_it_uses_safe_defaults_for_unclear_inputs(): void
    {
        $plan = (new AidApplicationPlanBuilder)->build(new AidApplicationInput(
            program: '',
            category: 'mystery',
            urgency: 'unknown',
            deadline: '',
            blocker: '',
            contact: '',
            document: '',
        ));

        $this->assertStringContainsString('this support option', $plan->firstAction);
        $this->assertContains('The letter, bill, or form', $plan->documents);
        $this->assertStringContainsString('the support office', $plan->script);
        $this->assertContains('What is the correct department or form?', $plan->questions);
    }
}
