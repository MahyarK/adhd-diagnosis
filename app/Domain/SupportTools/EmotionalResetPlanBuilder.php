<?php

namespace App\Domain\SupportTools;

final class EmotionalResetPlanBuilder
{
    /**
     * @var array<string, array<int, string>>
     */
    private const GROUNDING = [
        'low' => [
            'Name the feeling in one sentence.',
            'Take one slow breath before choosing an action.',
            'Do one useful step, not a full life review.',
        ],
        'medium' => [
            'Put both feet on the floor and unclench your jaw or shoulders.',
            'Name five things you can see or touch.',
            'Write the next action on one line only.',
        ],
        'high' => [
            'Pause decisions, messages, spending, and self-criticism for ten minutes.',
            'Do one body reset: water, food, medication routine, shower, walk, or rest.',
            'Ask for support or move near another person if being alone is making the spiral worse.',
        ],
    ];

    /**
     * @var array<string, string>
     */
    private const BODY_RESETS = [
        'tight' => 'Loosen one muscle group and exhale longer than you inhale.',
        'restless' => 'Move for two minutes before trying to think your way out.',
        'numb' => 'Use a small sensory cue: cold water, texture, light, or a warm drink.',
        'tired' => 'Lower the task to the smallest protective action.',
        'unknown' => 'Check the basics: water, food, medication routine, bathroom, rest, or movement.',
    ];

    public function build(EmotionalResetInput $input): EmotionalResetPlan
    {
        $intensity = array_key_exists($input->intensity, self::GROUNDING) ? $input->intensity : 'medium';
        $bodyState = array_key_exists($input->bodyState, self::BODY_RESETS) ? $input->bodyState : 'unknown';
        $trigger = $this->fallback($input->trigger, 'this moment');
        $story = $this->fallback($input->story, 'my brain is trying to protect me, not prove I am bad');
        $nextAction = $this->fallback($input->nextAction, 'do one tiny repair or reset step');
        $support = $this->fallback($input->support, 'someone safe');

        return new EmotionalResetPlan(
            trigger: $trigger,
            firstReset: self::BODY_RESETS[$bodyState],
            groundingSteps: self::GROUNDING[$intensity],
            kindReframe: "A kinder read: {$story}. This is a hard moment, not a final verdict about you.",
            nextAction: "Next tiny action: {$nextAction}. Keep it small enough to do while imperfect.",
            supportScript: "Hi {$support}, I am in a shame/overwhelm spiral about {$trigger}. I do not need fixing; could you help me do one small reset or stay with me for a few minutes?",
            stopRule: 'Stop the reset when your body is a little safer or the next action is clear. You do not have to solve the whole feeling before continuing.',
        );
    }

    private function fallback(string $value, string $fallback): string
    {
        $trimmed = trim($value);

        return $trimmed === '' ? $fallback : $trimmed;
    }
}
