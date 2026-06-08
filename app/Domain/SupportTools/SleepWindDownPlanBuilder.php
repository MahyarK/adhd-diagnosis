<?php

namespace App\Domain\SupportTools;

final class SleepWindDownPlanBuilder
{
    /**
     * @var array<string, array<int, string>>
     */
    private const STEPS = [
        'late' => [
            'Lower the room demand: dim light, reduce noise, and stop adding new tasks.',
            'Do the smallest hygiene version: bathroom, teeth or mouth rinse, face or hands, medication routine if prescribed.',
            'Move your body toward bed even if your mind is not convinced yet.',
        ],
        'revenge' => [
            'Name the feeling: “I want time that feels like mine.”',
            'Choose one tiny comfort on purpose, then put a finish line on it.',
            'Park the next fun thing for tomorrow so sleep is not the enemy.',
        ],
        'wired' => [
            'Dump open loops onto paper or notes without solving them.',
            'Use boring sensory input: low light, slow audio, stretching, or a warm/cool cue.',
            'Choose one body-calming step before checking anything else.',
        ],
        'early' => [
            'Start the wind-down before urgency hits.',
            'Prepare tomorrow’s launch item while your brain still has some fuel.',
            'Protect a clear stopping cue for screens, chores, or work.',
        ],
    ];

    public function build(SleepWindDownInput $input): SleepWindDownPlan
    {
        $mode = array_key_exists($input->mode, self::STEPS) ? $input->mode : 'late';
        $wakeTime = $this->fallback($input->wakeTime, 'the time you need to wake up');
        $blocker = $this->fallback($input->blocker, 'the thing keeping you up');
        $tomorrow = $this->fallback($input->tomorrow, 'one visible thing future-you needs');
        $screenRule = $this->fallback($input->screenRule, 'put the phone outside arm’s reach or switch to one boring mode');
        $comfort = $this->fallback($input->comfort, 'one small comfort cue');

        return new SleepWindDownPlan(
            firstAction: "Set a two-minute timer and do not negotiate with {$blocker}. Start by preparing {$comfort}.",
            steps: self::STEPS[$mode],
            screenBoundary: "Screen boundary: {$screenRule}. If you keep scrolling, make it seated somewhere less comfortable than bed.",
            tomorrowLaunch: "For {$wakeTime}: set out {$tomorrow}. Tomorrow only needs a launch pad, not a perfect morning.",
            rules: [
                'The goal is getting closer to sleep, not performing a perfect routine.',
                'No new chores after the first wind-down step unless they protect safety or tomorrow’s launch.',
                'If sleep does not come, rest still counts: keep the environment boring and body-led.',
            ],
            stopRule: 'Stop planning after the card is made. Begin the first action and let the routine be smaller than your ideal version.',
        );
    }

    private function fallback(string $value, string $fallback): string
    {
        $trimmed = trim($value);

        return $trimmed === '' ? $fallback : $trimmed;
    }
}
