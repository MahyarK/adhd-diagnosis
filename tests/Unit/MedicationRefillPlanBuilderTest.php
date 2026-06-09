<?php

namespace Tests\Unit;

use App\Domain\SupportTools\MedicationRefillInput;
use App\Domain\SupportTools\MedicationRefillPlanBuilder;
use PHPUnit\Framework\TestCase;

class MedicationRefillPlanBuilderTest extends TestCase
{
    public function test_it_builds_an_out_of_medicine_rescue_plan(): void
    {
        $plan = (new MedicationRefillPlanBuilder)->build(new MedicationRefillInput(
            mode: 'out',
            medicine: 'ADHD medication',
            supply: '0 pills left',
            blocker: 'I feel ashamed calling late',
            contact: 'my prescriber',
            deadline: 'tomorrow morning',
        ));

        $this->assertStringContainsString('ADHD medication', $plan->firstAction);
        $this->assertContains('Contact the pharmacist or prescriber today and say you are out.', $plan->steps);
        $this->assertStringContainsString('I feel ashamed calling late', implode(' ', $plan->steps));
        $this->assertStringContainsString('0 pills left', $plan->script);
        $this->assertStringContainsString('tomorrow morning', $plan->questions[2]);
        $this->assertStringContainsString('not medical advice', $plan->safetyNote);
    }

    public function test_it_uses_defaults_for_unclear_inputs(): void
    {
        $plan = (new MedicationRefillPlanBuilder)->build(new MedicationRefillInput(
            mode: '',
            medicine: '',
            supply: '',
            blocker: '',
            contact: '',
            deadline: '',
        ));

        $this->assertStringContainsString('my medication', $plan->firstAction);
        $this->assertContains('Check the bottle, app, or pharmacy label for remaining supply and refill status.', $plan->steps);
        $this->assertStringContainsString('my pharmacy or prescriber', $plan->script);
        $this->assertCount(4, $plan->questions);
    }
}
