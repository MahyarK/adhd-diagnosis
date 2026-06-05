<?php

namespace App\Domain\SupportTools;

final class TransitionRescuePlanBuilder
{
    /**
     * @var array<string, array<int, string>>
     */
    private const MODE_STEPS = [
        'leaving_home' => [
            'Put shoes, bag, keys, wallet, and medication or water in one visible launch spot.',
            'Check only the appointment/place/time, not every possible detail.',
            'Leave at the buffer time even if the house or task is unfinished.',
        ],
        'appointment_wait' => [
            'Write the appointment time and one question on a note.',
            'Choose one waiting activity that can stop instantly.',
            'Set an alarm for the move-now time and a second backup alarm.',
        ],
        'task_switch' => [
            'Write the current task restart point in one sentence.',
            'Close or park the current tab/object before opening the next task.',
            'Start the new task with a two-minute touch, not a full commitment.',
        ],
        'stuck_before_start' => [
            'Touch the first object, tab, document, door, or app involved.',
            'Do the first visible movement before planning more.',
            'Stop after two minutes if starting is the win today.',
        ],
        'bedtime' => [
            'Lower light or sound first; do not negotiate the whole night.',
            'Put tomorrow’s first item somewhere visible.',
            'Move to the sleep area before solving unfinished thoughts.',
        ],
        'unsure' => [
            'Name what you are waiting for or avoiding in one phrase.',
            'Choose the next physical location: desk, door, bed, kitchen, bathroom, or outside.',
            'Use a timer so the transition has an edge.',
        ],
    ];

    /**
     * @var array<string, string>
     */
    private const FIRST_ACTIONS = [
        'leaving_home' => 'Stand up and put the must-have items in one launch spot.',
        'appointment_wait' => 'Set the move-now alarm and write one question.',
        'task_switch' => 'Write the restart point for the current task.',
        'stuck_before_start' => 'Touch the first object connected to the task.',
        'bedtime' => 'Lower one stimulation source and move one step closer to bed.',
        'unsure' => 'Set a two-minute timer and choose the next physical place.',
    ];

    public function build(TransitionRescueInput $input): TransitionRescuePlan
    {
        $mode = array_key_exists($input->mode, self::MODE_STEPS) ? $input->mode : 'unsure';
        $transition = $this->fallback($input->transition, 'the next transition');
        $time = $this->fallback($input->time, 'the next move-now moment');
        $anchor = $this->fallback($input->anchor, 'one visible cue');
        $blocker = $this->fallback($input->blocker, 'unclear friction');
        $support = $this->fallback($input->support, 'someone safe');

        return new TransitionRescuePlan(
            transition: $transition,
            firstAction: self::FIRST_ACTIONS[$mode],
            steps: self::MODE_STEPS[$mode],
            parkingList: [
                "Park the blocker: {$blocker}. It does not need to be solved before moving.",
                "Use the anchor cue: {$anchor}. Put it where your eyes or hands will meet it.",
                "Protect the time edge: {$time}. Let the alarm decide when thinking stops.",
            ],
            anchorCue: "When I see or touch {$anchor}, I start moving toward {$transition}.",
            supportScript: "Hi {$support}, I am stuck in waiting/transition mode around {$transition}. Could you check in at {$time} or stay with me while I do the first two-minute move?",
            stopRule: 'Stop planning when the next physical movement is clear. Transition first, optimize later.',
        );
    }

    private function fallback(string $value, string $fallback): string
    {
        $trimmed = trim($value);

        return $trimmed === '' ? $fallback : $trimmed;
    }
}
