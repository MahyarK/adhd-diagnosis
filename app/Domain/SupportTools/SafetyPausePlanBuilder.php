<?php

namespace App\Domain\SupportTools;

final class SafetyPausePlanBuilder
{
    /**
     * @var array<string, array<int, string>>
     */
    private const GROUNDING_STEPS = [
        'low' => ['Put both feet on the floor and name one thing you can see.', 'Take one slow breath before choosing the next step.', 'Write only the next ten minutes, not the whole day.'],
        'medium' => ['Move near light, water, or another person if possible.', 'Name five things you can see or touch.', 'Delay big decisions, messages, spending, and self-criticism for ten minutes.'],
        'high' => ['Move away from anything you could use to hurt yourself.', 'Call, text, or sit near another person now.', 'Use 988 in the U.S. or local emergency/crisis support if you might not stay safe.'],
    ];

    /**
     * @var array<string, array<int, string>>
     */
    private const NEXT_STEPS = [
        'safe' => ['Do one body support action: water, food, medication routine, bathroom, warmth, cooling, or rest.', 'Pick one tiny stabilizing task that makes the next hour easier.', 'Save or share this card if the feeling may come back later.'],
        'unsure' => ['Do not stay alone with uncertainty if safety might drop.', 'Contact one person or crisis line and say you are not sure you can stay okay.', 'Make the environment safer before solving the original problem.'],
        'unsafe' => ['Use live support now: call/text 988 in the U.S., local emergency services, or a trusted person who can be with you.', 'Move away from weapons, medication stockpiles, heights, traffic, or anything you could use to hurt yourself.', 'Do not negotiate with the crisis alone; let another person or service hold the next step.'],
        'supporting' => ['Stay with the person or keep them connected to live support.', 'Ask directly if they can stay safe right now, then reduce access to immediate danger if you can do so safely.', 'Contact local emergency/crisis support if there is immediate danger.'],
    ];

    public function build(SafetyPauseInput $input): SafetyPausePlan
    {
        $state = array_key_exists($input->state, self::NEXT_STEPS) ? $input->state : 'unsure';
        $intensity = array_key_exists($input->intensity, self::GROUNDING_STEPS) ? $input->intensity : 'medium';
        $support = trim($input->support) !== '' ? trim($input->support) : 'someone who can respond now';
        $location = trim($input->location) !== '' ? trim($input->location) : 'where I am';
        $nextStep = trim($input->nextStep) !== '' ? trim($input->nextStep) : 'stay connected to support for the next ten minutes';
        $barrier = trim($input->barrier);

        $urgent = in_array($state, ['unsafe', 'unsure'], true) || $intensity === 'high';
        $barrierLine = $barrier !== '' ? " If the barrier shows up, say it plainly: {$barrier}." : '';

        return new SafetyPausePlan(
            immediate: $urgent
                ? 'If there is immediate danger or you might hurt yourself or someone else, call local emergency services now. In the U.S., call or text 988 for the Suicide & Crisis Lifeline.'
                : 'This sounds like an overwhelmed-but-not-immediate-danger moment. Slow the next ten minutes down and choose one stabilizing action.',
            firstAction: "First action: move to {$location}, lower stimulation if you can, and keep the next step to: {$nextStep}.{$barrierLine}",
            supportScript: "Hi {$support}, I am having a hard moment and I do not want to handle it alone. Can you stay with me, call me, or help me take the next safe step?",
            groundingSteps: self::GROUNDING_STEPS[$intensity],
            nextSteps: self::NEXT_STEPS[$state],
            stopRule: 'Stop planning and switch to live help if safety gets worse, you feel at risk of acting on an urge, or you cannot stay connected to another person.',
        );
    }
}
