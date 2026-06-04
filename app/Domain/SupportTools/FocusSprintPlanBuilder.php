<?php

namespace App\Domain\SupportTools;

final class FocusSprintPlanBuilder
{
    /**
     * @var array<string, array<int, string>>
     */
    private const STEPS = [
        'start' => [
            'Open or touch the task.',
            'Do the first visible action only.',
            'Mark the sprint done when the timer ends, even if the task is not finished.',
        ],
        'continue' => [
            'Read the last visible thing you did.',
            'Choose the next small section.',
            'Stop at the timer and write the restart point.',
        ],
        'boring' => [
            'Add one stimulation support: music, standing, timer, or body double.',
            'Do the task badly for two minutes before improving anything.',
            'Keep the finish line at the timer, not at perfect completion.',
        ],
        'avoidance' => [
            'Name the feeling without solving it.',
            'Make the task less private: share the sprint or sit near someone.',
            'Do the least scary visible step.',
        ],
    ];

    public function build(FocusSprintInput $input): FocusSprintPlan
    {
        $mode = array_key_exists($input->mode, self::STEPS) ? $input->mode : 'start';
        $task = $this->fallback($input->task, 'one task');
        $minutes = $this->fallback($input->minutes, '10');
        $distraction = $this->fallback($input->distraction, 'write it on a parking note');
        $support = $this->fallback($input->support, 'quiet timer');
        $reward = $this->fallback($input->reward, 'take a short guilt-free pause');

        return new FocusSprintPlan(
            task: $task,
            startCue: "Set a {$minutes}-minute timer and begin: {$task}.",
            steps: self::STEPS[$mode],
            distractionRule: "If a distraction appears, {$distraction}, then return to the timer.",
            supportPrompt: "Support for this sprint: {$support}.",
            reward: "When the timer ends: {$reward}.",
            stopRule: 'Stop when the timer ends, or choose one more sprint on purpose. Do not let a sprint secretly become the whole day.',
        );
    }

    private function fallback(string $value, string $fallback): string
    {
        $trimmed = trim($value);

        return $trimmed === '' ? $fallback : $trimmed;
    }
}
