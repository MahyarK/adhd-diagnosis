<?php

namespace App\Domain\SupportTools;

final class AccountabilityPlanBuilder
{
    /**
     * @var array<string, string>
     */
    private const FORMATS = [
        'text' => 'send a quick text check-in',
        'call' => 'do a short call or voice note',
        'body_double' => 'sit together or join a focus call while the first step happens',
        'shared_space' => 'work in the same room, library, cafe, or online coworking space',
    ];

    public function build(AccountabilityInput $input): AccountabilityPlan
    {
        $task = $this->fallback($input->task, 'the next task');
        $person = $this->fallback($input->person, 'someone safe');
        $format = self::FORMATS[$input->format] ?? self::FORMATS['text'];
        $time = $this->fallback($input->time, 'the planned start time');
        $proof = $this->fallback($input->proof, 'one photo, screenshot, checkmark, or sentence');
        $missed = $this->fallback($input->missed, 'send a reset message and choose the next smaller start');

        return new AccountabilityPlan(
            script: "Hi {$person}, I am trying to start {$task} at {$time}. Could we {$format}? I only need help starting, not pressure to finish.",
            steps: [
                "Before {$time}: make the first step visible and remove one avoidable friction.",
                "At {$time}: start {$task} for two minutes while {$person} witnesses or checks in.",
                "After the start: send {$proof} as proof of progress.",
            ],
            proof: "Proof of progress: {$proof}. It should be tiny enough to send even if the task is not done.",
            missedPlan: "If the check-in is missed: {$missed}. No shame recap required.",
            rules: [
                'Ask for witnessing, not policing.',
                'Keep the check-in short so it does not become another task.',
                'Reward the start and decide on any next block separately.',
            ],
        );
    }

    private function fallback(string $value, string $fallback): string
    {
        $trimmed = trim($value);

        return $trimmed === '' ? $fallback : $trimmed;
    }
}
