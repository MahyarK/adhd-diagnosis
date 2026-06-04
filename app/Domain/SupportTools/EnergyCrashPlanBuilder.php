<?php

namespace App\Domain\SupportTools;

final class EnergyCrashPlanBuilder
{
    /**
     * @var array<string, array<int, string>>
     */
    private const STEPS = [
        'empty' => [
            'Lower the target to the smallest version that prevents harm or creates clarity.',
            'Do one body-support action before touching the task.',
            'Use a timer for the next visible step only.',
            'Stop or ask for help before the crash gets worse.',
        ],
        'overwhelmed' => [
            'Write every floating task on one messy list.',
            'Circle only the item with the biggest consequence or relief.',
            'Move everything else to a later list.',
            'Start the circled item with a two-minute action.',
        ],
        'stuck' => [
            'Name what is blocking the start.',
            'Remove one hidden step: link, object, document, message, or decision.',
            'Do the setup step separately from the task.',
            'Restart with a version small enough to look almost silly.',
        ],
        'wired' => [
            'Discharge some speed with movement, water, or a short reset.',
            'Pick the task that should not wait until tomorrow.',
            'Make a visible checklist with three items max.',
            'Stop before turning urgency into a whole-night sprint.',
        ],
    ];

    /**
     * @var array<string, string>
     */
    private const BODY_RESETS = [
        'food' => 'Eat something easy or put food where you can reach it.',
        'water' => 'Drink water or place a full glass next to the task.',
        'meds' => 'Check whether medication, supplements, or a health routine is due.',
        'rest' => 'Set a short rest timer and let the task wait without negotiating with it.',
        'movement' => 'Move for two minutes: stand, stretch, walk, or shake out tension.',
        'shower' => 'Use a reset cue: wash face, brush teeth, shower, or change clothes.',
    ];

    public function build(EnergyCrashInput $input): EnergyCrashPlan
    {
        $state = $this->knownKey($input->state, self::STEPS, 'empty');
        $bodyNeed = $this->knownKey($input->bodyNeed, self::BODY_RESETS, 'water');
        $must = $this->fallback($input->must, 'one task that matters today');
        $drop = $this->fallback($input->drop, 'anything that is not urgent, unsafe, or promised for today');
        $support = $this->fallback($input->support, 'someone who can make the next step less lonely');
        $time = $this->fallback($input->time, '10 minutes');

        return new EnergyCrashPlan(
            firstAction: "For the next {$time}, only start: {$must}. Do not solve the whole day.",
            bodyReset: self::BODY_RESETS[$bodyNeed],
            steps: self::STEPS[$state],
            dropRule: "Drop, delay, or simplify: {$drop}. Low capacity is a reason to reduce scope.",
            supportScript: "Hi {$support}, I am low on capacity and trying to do {$must}. Could you help me with one small next step or check in after {$time}?",
            stopRule: "Stop when the next step is clearer, the timer ends, or your body says this is no longer safe to push.",
        );
    }

    /**
     * @param  array<string, mixed>  $known
     */
    private function knownKey(string $value, array $known, string $fallback): string
    {
        return array_key_exists($value, $known) ? $value : $fallback;
    }

    private function fallback(string $value, string $fallback): string
    {
        $trimmed = trim($value);

        return $trimmed === '' ? $fallback : $trimmed;
    }
}
