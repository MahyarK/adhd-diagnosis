<?php

namespace App\Domain\SupportTools;

final class FollowUpRescuePlanBuilder
{
    /**
     * @var array<string, array<int, string>>
     */
    private const STEPS = [
        'medical' => ['Find the last portal message, referral, prescription, or appointment note.', 'Ask for the current status and the next required action.', 'Write the answer in one place before closing the portal or phone.'],
        'admin' => ['Find the latest email, letter, bill, or case number.', 'Ask what is still needed and whether anything is overdue.', 'Save the reference number, deadline, or next office to contact.'],
        'application' => ['Find the application name, portal, or office contact.', 'Ask whether the application is active, missing documents, or waiting for review.', 'Set a follow-up date before leaving the page.'],
        'message' => ['Open the thread without rereading everything.', 'Send the short catch-up script or answer only the next needed thing.', 'Archive, flag, or write the next reply date.'],
        'appointment' => ['Find the booking link, phone number, or old appointment note.', 'Ask to book, rebook, cancel, or confirm the next available option.', 'Put the date, time, and prep step somewhere visible.'],
        'unsure' => ['Find the last place this existed: inbox, portal, notes, calendar, or paper pile.', 'Identify one person, office, or system that can tell you the status.', 'Ask "what is the next step now?" instead of trying to solve the whole story.'],
    ];

    /**
     * @var array<string, array<int, string>>
     */
    private const QUESTIONS = [
        'medical' => ['What is the current status?', 'What should I do next, and by when?'],
        'admin' => ['Is anything missing, overdue, or paused?', 'What reference number or deadline should I write down?'],
        'application' => ['Is my application still active?', 'Can I submit missing documents later?'],
        'message' => ['What do you need from me right now?', 'Would a short answer be enough to move this forward?'],
        'appointment' => ['What is the next available option?', 'What should I bring, prepare, or confirm?'],
        'unsure' => ['Who can tell me the current status?', 'What is the smallest next step to keep this alive?'],
    ];

    public function build(FollowUpRescueInput $input): FollowUpRescuePlan
    {
        $kind = array_key_exists($input->kind, self::STEPS) ? $input->kind : 'unsure';
        $thing = trim($input->thing) !== '' ? trim($input->thing) : 'this follow-up';
        $person = trim($input->person) !== '' ? trim($input->person) : 'the right person or office';
        $blocker = trim($input->blocker);
        $nextDate = trim($input->nextDate) !== '' ? trim($input->nextDate) : 'the next check-in date';

        $latenessLine = match ($input->lateness) {
            'today' => 'This is still recoverable today.',
            'week' => 'A week late still deserves a simple re-entry.',
            'long' => 'Even if it has been a long time, status is better than guessing.',
            default => 'You do not need to know how late it is to ask what happens next.',
        };

        $blockerLine = $blocker !== '' ? " If the blocker appears, name it once: {$blocker}." : '';

        return new FollowUpRescuePlan(
            firstAction: "Open or find one trace of {$thing}: inbox, portal, calendar, paper, notes, or call log. {$latenessLine}{$blockerLine}",
            script: "Hi {$person}, I am catching up on {$thing}. I may have missed a step, and I want to handle the next useful action. Could you tell me the current status and what I should do next?",
            steps: [
                ...self::STEPS[$kind],
                "Save {$nextDate} as the next follow-up point.",
            ],
            questions: [
                ...self::QUESTIONS[$kind],
                'Can you confirm the next step in writing?',
            ],
            stopRule: 'Stop after one status check, one message sent, or one next date saved. Follow-up rescue is re-entry, not self-punishment.',
        );
    }
}
