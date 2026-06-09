<?php

namespace App\Domain\SupportTools;

final class ErrandLaunchPlanBuilder
{
    /**
     * @var array<string, array<int, string>>
     */
    private const KIND_STEPS = [
        'appointment' => [
            'Confirm the exact time, address, provider name, and whether online check-in is needed.',
            'Write one sentence for why you are going and one question you do not want to forget.',
            'Set a reminder for the moment you need to leave, not only the appointment time.',
        ],
        'pharmacy_store' => [
            'Name the one item or counter you are going for before adding extra shopping.',
            'Check opening hours, pickup code, prescription status, or stock if that might block the trip.',
            'Decide the minimum successful version: pick up, ask one question, or buy only the needed item.',
        ],
        'paperwork' => [
            'Find the form, ID, card, envelope, or document involved before leaving.',
            'Take a photo or backup copy if losing the paper would create another problem.',
            'Write the receiving desk, mailbox, office, or upload page as the destination.',
        ],
        'school_work' => [
            'Identify the door, room, desk, person, or login that counts as arrival.',
            'Prepare one sentence for why you are there if speaking is part of the task.',
            'Choose the minimum arrival proof: checked in, messaged, badge scanned, or item dropped off.',
        ],
        'unsure' => [
            'Write what “done enough” means in one plain sentence.',
            'Identify the next person, place, counter, app, or website that can tell you what happens next.',
            'Ask for the next step instead of trying to solve the whole errand before starting.',
        ],
    ];

    /**
     * @var array<string, string>
     */
    private const TRAVEL_STEPS = [
        'walk_bike' => 'Check weather, route time, and the safest path. Put shoes, keys, phone, and water where your body can start moving.',
        'public_transport' => 'Check the next departure and set a leave-now alarm with buffer for walking, tickets, and platform changes.',
        'car_ride' => 'Check keys, parking, fuel or ride status, address, and one backup if traffic or the ride changes.',
        'online_phone' => 'Open the call link, portal, email, or phone number. Put the script beside it before switching apps.',
        'unsure' => 'Choose the lowest-friction route available today and protect a backup contact option.',
    ];

    public function build(ErrandLaunchInput $input): ErrandLaunchPlan
    {
        $destination = $this->fallback($input->destination, 'the errand');
        $kind = array_key_exists($input->kind, self::KIND_STEPS) ? $input->kind : 'unsure';
        $travel = array_key_exists($input->travel, self::TRAVEL_STEPS) ? $input->travel : 'unsure';
        $deadline = $this->fallback($input->deadline, 'the time you need to leave or start');
        $bring = $this->bringList($input->bring);

        $steps = [
            "Set one alarm for {$deadline} and one backup alarm five minutes earlier.",
            self::TRAVEL_STEPS[$travel],
            ...self::KIND_STEPS[$kind],
        ];

        if (trim($input->blocker) !== '') {
            $steps[] = "If this blocker appears, use the backup plan instead of restarting from zero: {$input->blocker}.";
        }

        return new ErrandLaunchPlan(
            firstAction: "Put shoes on or open the contact app, then place everything for {$destination} in one launch spot for two minutes.",
            steps: $steps,
            bringList: $bring,
            lateScript: "Hi, I am on my way to {$destination} but may be late. Should I still come, switch to a later time, or do one step by phone/message?",
            backupPlan: [
                'If leaving is no longer realistic, contact the person/place before avoiding it silently.',
                'Ask what the next smallest acceptable step is: reschedule, partial payment, drop-off, phone call, portal upload, or new deadline.',
                'Write the new date, time, or next action somewhere visible before closing the app.',
            ],
            stopRule: 'Stop preparing when the essentials are gathered and the next movement is clear. Do not add extra errands unless they were already ready.',
        );
    }

    private function fallback(string $value, string $fallback): string
    {
        $trimmed = trim($value);

        return $trimmed === '' ? $fallback : $trimmed;
    }

    /**
     * @return array<int, string>
     */
    private function bringList(string $bring): array
    {
        $items = array_values(array_filter(array_map(
            static fn (string $item): string => trim($item),
            preg_split('/[\r\n,]+/', $bring) ?: [],
        )));

        return $items === []
            ? ['Phone', 'keys', 'wallet/payment card', 'ID or insurance card if needed', 'one relevant document or note']
            : $items;
    }
}
