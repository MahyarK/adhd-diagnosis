<?php

namespace App\Domain\SupportTools;

final class LaundryRescuePlanBuilder
{
    /**
     * @var array<string, array<int, string>>
     */
    private const MODE_STEPS = [
        'wearable' => [
            'Find one wearable outfit or the closest pieces first.',
            'Only wash what protects tomorrow: underwear, socks, shirt, work/school item, or towel.',
            'Skip sorting unless something could bleed color or be damaged.',
        ],
        'wash' => [
            'Start one small load instead of collecting every item.',
            'Put detergent in immediately after loading so the washer can start.',
            'Set a timer for the moment the load needs moving.',
        ],
        'dry' => [
            'Find the wet load and move it before starting anything else.',
            'Dry only the items that matter soon if the full load feels too big.',
            'Set a second timer for drying or hanging.',
        ],
        'put_away' => [
            'Make one clean pile: wearable now, not perfect storage.',
            'Put away only the items that block the floor, bed, or tomorrow.',
            'Use bins or zones if folding stops the whole process.',
        ],
    ];

    public function build(LaundryRescueInput $input): LaundryRescuePlan
    {
        $mode = array_key_exists($input->mode, self::MODE_STEPS) ? $input->mode : 'wearable';
        $needed = $this->fallback($input->needed, 'one wearable outfit');
        $deadline = $this->fallback($input->deadline, 'the next time you need clean clothes');
        $blocker = $this->fallback($input->blocker, 'the laundry friction');

        return new LaundryRescuePlan(
            firstAction: "Set a two-minute timer and collect only {$needed}. Ignore the full laundry story for now.",
            steps: [
                ...self::MODE_STEPS[$mode],
                $this->machineStep($input->machine),
                $this->energyStep($input->energy),
                "Park the blocker without solving it: {$blocker}.",
            ],
            dryingPlan: "Drying plan: move or hang the needed clothes first, then set a visible timer or note for {$deadline}.",
            emergencyOutfit: "Emergency outfit: choose the cleanest good-enough combination for {$deadline}. Re-wearing is allowed when it is safe and not visibly dirty.",
            minimums: [
                'Clean enough beats fully caught up.',
                'A basket, chair, or clear bag can be a temporary clean zone.',
                'Do not start a second load until the first load has a next destination.',
            ],
            stopRule: 'Stop when the next wearable outfit is protected or one load has a timer. Laundry rescue is not whole-home recovery.',
        );
    }

    private function machineStep(string $machine): string
    {
        return match ($machine) {
            'home' => 'If you have a home machine, start it before leaving the laundry area.',
            'shared' => 'If laundry is shared, gather payment/key/detergent before carrying clothes.',
            'sink' => 'If hand-washing, wash only the urgent pieces and roll them in a towel before hanging.',
            default => 'Use the lowest-friction laundry option available today.',
        };
    }

    private function energyStep(string $energy): string
    {
        return match ($energy) {
            'none' => 'No-energy version: identify the outfit and do only odor check, spot clean, or airing out.',
            'low' => 'Low-energy version: one small load, no folding, clean pile allowed.',
            'some' => 'Some-energy version: wash, dry, and reset one basket or surface.',
            default => 'Choose the smallest version that creates wearable clothes.',
        };
    }

    private function fallback(string $value, string $fallback): string
    {
        $trimmed = trim($value);

        return $trimmed === '' ? $fallback : $trimmed;
    }
}
