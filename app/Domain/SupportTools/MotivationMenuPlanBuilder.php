<?php

namespace App\Domain\SupportTools;

final class MotivationMenuPlanBuilder
{
    /**
     * @var array<string, array<int, string>>
     */
    private const MENUS = [
        'calm' => [
            'Make a warm drink and take the first sip before starting.',
            'Use a soft playlist, low light, or a quieter spot.',
            'After the block, take five minutes with no input.',
        ],
        'novelty' => [
            'Change location, pen, playlist, or browser window before the first step.',
            'Turn the task into a timed challenge: beat two minutes, not the whole task.',
            'After the block, save one visible progress mark.',
        ],
        'social' => [
            'Tell someone the two-minute start before you begin.',
            'Use a body double, focus call, coworking room, or quiet shared space.',
            'After the block, send a tiny done message.',
        ],
        'movement' => [
            'Start standing, pacing, stretching, or using a fidget.',
            'Put the task where movement is allowed.',
            'After the block, take a short movement break on purpose.',
        ],
    ];

    /**
     * @var array<string, string>
     */
    private const STIMULATION = [
        'quiet' => 'Pair the task with low stimulation: silence, simple timer, clean surface, or one tab.',
        'music' => 'Pair the task with music or ambient sound that does not steal the task.',
        'body_double' => 'Pair the task with another person nearby, a focus call, or a check-in message.',
        'movement' => 'Pair the task with legal movement: standing, walking, stretching, or fidgeting.',
    ];

    public function build(MotivationMenuInput $input): MotivationMenuPlan
    {
        $task = $this->fallback($input->task, 'the avoided task');
        $rewardType = array_key_exists($input->rewardType, self::MENUS) ? $input->rewardType : 'novelty';
        $stimulation = array_key_exists($input->stimulation, self::STIMULATION) ? $input->stimulation : 'music';
        $mood = $this->fallback($input->mood, 'low motivation');
        $friction = $this->fallback($input->friction, 'starting feels too big');
        $costLimit = $this->fallback($input->costLimit, 'free or already available');

        return new MotivationMenuPlan(
            starter: "Start {$task} for two minutes while your mood is {$mood}. The reward is for starting, not finishing.",
            menu: self::MENUS[$rewardType],
            taskPairing: self::STIMULATION[$stimulation],
            frictionPlan: "If {$friction}, shrink {$task} until the first move is visible and costs {$costLimit}.",
            rules: [
                'Use free, already-owned, or low-risk rewards first.',
                'Avoid reward traps that make the task harder to return to: endless scrolling, spending, or losing sleep.',
                'Stop after the planned block or choose another block on purpose.',
            ],
        );
    }

    private function fallback(string $value, string $fallback): string
    {
        $trimmed = trim($value);

        return $trimmed === '' ? $fallback : $trimmed;
    }
}
