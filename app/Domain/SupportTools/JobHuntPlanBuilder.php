<?php

namespace App\Domain\SupportTools;

final class JobHuntPlanBuilder
{
    /**
     * @var array<string, array<int, string>>
     */
    private const STEPS = [
        'resume' => ['Open the latest resume or profile, even if it is outdated.', 'Update only the top third: title, contact, and one recent proof of skill.', 'Export or save one usable version before editing more.'],
        'application' => ['Open the job post and copy the deadline, title, and link into one note.', 'Answer the easiest required field first.', 'Submit the good-enough version or save the exact restart point.'],
        'interview' => ['Find the interview time, link, location, and contact person.', 'Write three proof stories: problem, action, result.', 'Prepare one question about schedule, expectations, pay range, or support.'],
        'followup' => ['Find the last message, application portal, or recruiter contact.', 'Send a short status check or thank-you message.', 'Save the next follow-up date before closing the inbox.'],
        'income' => ['Name the fastest money-protecting action: apply, call, ask, follow up, or update documents.', 'Choose one realistic target instead of searching every option.', 'Ask one person or office about the next paid or support step.'],
        'unsure' => ['Choose whether the next useful move is resume, application, interview, follow-up, or income protection.', 'Open only one tab, file, or message connected to that move.', 'Write the restart point before switching tasks.'],
    ];

    /**
     * @var array<string, array<int, string>>
     */
    private const MATERIALS = [
        'resume' => ['Latest resume or profile', 'One recent job, school, volunteer, or life example', 'Contact details'],
        'application' => ['Job link or posting', 'Resume or profile', 'One sentence about why this role fits'],
        'interview' => ['Time, link, or address', 'Three proof stories', 'Questions about pay, schedule, expectations, or support'],
        'followup' => ['Last email or portal status', 'Contact name if available', 'One next follow-up date'],
        'income' => ['List of immediate bills or income needs', 'One job/support option', 'Contact details for one person or office'],
        'unsure' => ['One target role or support option', 'One document or message to open', 'A place to save the restart point'],
    ];

    public function build(JobHuntInput $input): JobHuntPlan
    {
        $stage = array_key_exists($input->stage, self::STEPS) ? $input->stage : 'unsure';
        $target = trim($input->target) !== '' ? trim($input->target) : 'this job or income step';
        $contact = trim($input->contact) !== '' ? trim($input->contact) : 'the hiring contact, recruiter, support office, or a trusted person';
        $deadline = trim($input->deadline) !== '' ? trim($input->deadline) : 'the next check-in time';
        $blocker = trim($input->blocker);

        $energyLine = match ($input->energy) {
            'low' => 'Keep this to one tiny income-protecting move.',
            'medium' => 'Do the useful version before polishing.',
            'urgent' => 'Protect speed and clarity over perfect wording.',
            default => 'Choose one visible restart point before doing more research.',
        };

        $blockerLine = $blocker !== '' ? " If the blocker appears, park it in one sentence: {$blocker}." : '';

        return new JobHuntPlan(
            firstAction: "Set a ten-minute timer and open one thing connected to {$target}. {$energyLine}{$blockerLine}",
            script: "Hi {$contact}, I am working on {$target} and want to keep the next step moving. Could you confirm what is needed next, the deadline, or the best person to contact?",
            steps: [
                ...self::STEPS[$stage],
                "Save {$deadline} as the next job-hunt check-in.",
            ],
            materials: self::MATERIALS[$stage],
            stopRule: 'Stop after one application submitted, one message sent, one resume section updated, or one restart point saved. Job hunt rescue is momentum, not proving your worth.',
        );
    }
}
