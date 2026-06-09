<?php

namespace App\Domain\SupportTools;

final class MedicationRefillPlanBuilder
{
    /**
     * @var array<string, array<int, string>>
     */
    private const MODE_STEPS = [
        'running_low' => [
            'Check the bottle, app, or pharmacy label for remaining supply and refill status.',
            'Request the refill through the easiest official route: pharmacy app, phone, prescriber portal, or in person.',
            'Put pickup, delivery, or follow-up time into a reminder before closing the app.',
        ],
        'out' => [
            'Contact the pharmacist or prescriber today and say you are out.',
            'Ask what the safest next step is instead of guessing or changing the dose yourself.',
            'Write down who you contacted, the time, and what they told you.',
        ],
        'blocked' => [
            'Name the block: cost, stock, refill too soon, prior authorization, no appointment, or lost bottle.',
            'Ask the pharmacy what they can do and what needs the prescriber or insurer.',
            'Ask one support person to sit with you while you make the call or message.',
        ],
        'routine' => [
            'Choose one visible anchor for future refills: same day weekly, pill organizer check, or pharmacy app reminder.',
            'Set a refill check reminder before the supply is close to empty.',
            'Write where refill information lives so it does not depend on memory.',
        ],
    ];

    public function build(MedicationRefillInput $input): MedicationRefillPlan
    {
        $mode = array_key_exists($input->mode, self::MODE_STEPS) ? $input->mode : 'running_low';
        $medicine = $this->fallback($input->medicine, 'my medication');
        $supply = $this->fallback($input->supply, 'unknown supply');
        $contact = $this->fallback($input->contact, 'my pharmacy or prescriber');
        $deadline = $this->fallback($input->deadline, 'today');
        $blocker = trim($input->blocker);
        $steps = self::MODE_STEPS[$mode];

        if ($blocker !== '') {
            $steps[] = "Park the blocker as an ask, not a personal failure: {$blocker}.";
        }

        return new MedicationRefillPlan(
            firstAction: "Set a two-minute timer and find the bottle, pharmacy app, or latest message for {$medicine}. Current supply: {$supply}.",
            steps: $steps,
            script: "Hi, I am trying to prevent a gap with {$medicine}. My current supply is {$supply}. Could you tell me the next safest refill step and whether anything is needed from {$contact}?",
            questions: [
                'Is a refill available now, or does the prescriber need to approve it?',
                'Is there a cost, stock, insurance, or prior authorization issue?',
                "What should I do if I cannot get it before {$deadline}?",
                'Can you help me set up reminders, delivery, automatic refill, or a refill date if available?',
            ],
            safetyNote: 'This tool is for organizing refill steps, not medical advice. Ask a pharmacist, prescriber, or qualified clinician before changing, skipping, stopping, or restarting medication.',
            stopRule: 'Stop when the refill is requested, the next contact is written down, or a pharmacist/prescriber has told you the next step.',
        );
    }

    private function fallback(string $value, string $fallback): string
    {
        $trimmed = trim($value);

        return $trimmed === '' ? $fallback : $trimmed;
    }
}
