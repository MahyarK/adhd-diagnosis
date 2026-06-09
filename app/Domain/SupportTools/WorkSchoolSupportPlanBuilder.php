<?php

namespace App\Domain\SupportTools;

final class WorkSchoolSupportPlanBuilder
{
    /**
     * @var array<string, array<int, string>>
     */
    private const CHALLENGE_OPTIONS = [
        'focus' => [
            'Written priorities before starting a work block or study session.',
            'A quieter workspace, headphones, camera-off focus block, or reduced interruption window when possible.',
            'Shorter timed work blocks with a visible finish line.',
        ],
        'deadlines' => [
            'A midpoint check-in before the final deadline.',
            'Clear written definition of what “done enough” means.',
            'Permission to confirm priorities when several deadlines compete.',
        ],
        'memory' => [
            'Written instructions or recap after verbal conversations.',
            'Shared checklist, task board, calendar invite, or reminder that does not depend on memory alone.',
            'One agreed place for links, files, due dates, and decisions.',
        ],
        'overwhelm' => [
            'One task at a time with the next step separated from the full workload.',
            'A short reset break before continuing after overload.',
            'A clear escalation path for asking “what matters first?”',
        ],
        'transitions' => [
            'A five-minute warning before switching tasks, classes, meetings, or locations.',
            'A closing checklist for ending the current task before starting the next one.',
            'A predictable start cue, such as written agenda, first problem, or first ticket.',
        ],
        'communication' => [
            'A written summary of decisions after meetings or feedback.',
            'Permission to ask clarifying questions without it being treated as resistance.',
            'A response-time expectation so messages do not all feel equally urgent.',
        ],
    ];

    /**
     * @var array<string, string>
     */
    private const STYLE_OPTIONS = [
        'written' => 'Put the agreement in writing so it does not rely on working memory.',
        'checkin' => 'Use a short check-in to catch drift before it becomes a crisis.',
        'environment' => 'Change the environment before asking for more willpower.',
        'flexibility' => 'Make the finish line clearer or more flexible without removing accountability.',
        'body_double' => 'Use quiet co-working, a focus room, or a start-together moment for activation.',
        'planning' => 'Separate planning from doing so the first action is visible.',
    ];

    public function build(WorkSchoolSupportInput $input): WorkSchoolSupportPlan
    {
        $setting = $input->setting === 'school' ? 'school' : 'work';
        $place = $setting === 'school' ? 'school' : 'work';
        $challenge = array_key_exists($input->challenge, self::CHALLENGE_OPTIONS) ? $input->challenge : 'focus';
        $supportStyle = array_key_exists($input->supportStyle, self::STYLE_OPTIONS) ? $input->supportStyle : 'written';
        $friction = $this->fallback($input->friction, 'important details or next steps disappear');
        $person = $this->fallback($input->person, $setting === 'school' ? 'teacher, advisor, or support office' : 'manager, HR, or trusted lead');
        $trial = $this->fallback($input->trial, 'two weeks');

        return new WorkSchoolSupportPlan(
            firstAsk: "Pick one {$place} support to test for {$trial}; do not ask for a complete life redesign.",
            supportOptions: [
                self::STYLE_OPTIONS[$supportStyle],
                ...self::CHALLENGE_OPTIONS[$challenge],
            ],
            script: "Hi {$person}, I am trying to handle {$place} more reliably. The main friction is that {$friction}. Could we test one support for {$trial} and review what helped?",
            trialPlan: "Try one support for {$trial}. Keep the support visible, use it on real tasks, and review what changed before adding another support.",
            proofPoints: [
                'What became easier to start, remember, finish, or communicate?',
                'What still created friction?',
                'What should stay, change, or stop for the next trial?',
            ],
            boundary: 'This is a practical support plan, not legal advice or a guarantee. Keep the ask specific, respectful, and easy to review.',
        );
    }

    private function fallback(string $value, string $fallback): string
    {
        $trimmed = trim($value);

        return $trimmed === '' ? $fallback : $trimmed;
    }
}
