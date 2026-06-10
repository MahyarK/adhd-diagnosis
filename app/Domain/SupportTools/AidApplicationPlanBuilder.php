<?php

namespace App\Domain\SupportTools;

final class AidApplicationPlanBuilder
{
    /**
     * @var array<string, array<int, string>>
     */
    private const DOCUMENTS = [
        'medical' => ['Photo ID', 'Insurance card or denial letter', 'Income proof', 'Recent bill or estimate'],
        'school' => ['Student ID', 'Diagnosis or screening notes if available', 'Class schedule', 'One example of the support need'],
        'work' => ['Job title or schedule', 'Written support request', 'One work impact example', 'Relevant policy or HR contact'],
        'benefits' => ['Photo ID', 'Income proof', 'Address proof', 'Any case number or past letter'],
        'debt' => ['Bill or collection letter', 'Account number', 'Income proof', 'Hardship note'],
        'housing' => ['Lease or housing letter', 'Income proof', 'Urgency notice if any', 'Contact details for the office'],
        'unsure' => ['Photo ID', 'Income proof if relevant', 'The letter, bill, or form', 'One note about what help you need'],
    ];

    /**
     * @var array<string, array<int, string>>
     */
    private const QUESTIONS = [
        'medical' => ['Is charity care, sliding scale, or a payment plan available?', 'Which documents are required before an appointment or bill review?'],
        'school' => ['Who receives accommodation requests?', 'Can I submit a partial request while I gather documents?'],
        'work' => ['Who handles accommodation or adjustment requests?', 'Can we try one temporary support and review it later?'],
        'benefits' => ['What is the next required document?', 'Can I submit now and add missing documents later?'],
        'debt' => ['Can fees, interest, or collections be paused while I apply?', 'Is there a hardship plan or minimum payment option?'],
        'housing' => ['Is there emergency assistance or a prevention program?', 'What proof is needed to avoid a missed deadline?'],
        'unsure' => ['What is the correct department or form?', 'What is the smallest next step to keep this moving?'],
    ];

    public function build(AidApplicationInput $input): AidApplicationPlan
    {
        $category = array_key_exists($input->category, self::DOCUMENTS) ? $input->category : 'unsure';
        $program = trim($input->program) !== '' ? trim($input->program) : 'this support option';
        $contact = trim($input->contact) !== '' ? trim($input->contact) : 'the support office';
        $document = trim($input->document);
        $deadline = trim($input->deadline);
        $blocker = trim($input->blocker);

        $documents = self::DOCUMENTS[$category];

        if ($document !== '') {
            array_unshift($documents, $document);
            $documents = array_values(array_unique($documents));
        }

        $urgencyNote = match ($input->urgency) {
            'today' => 'Because this is urgent, contact the office before trying to complete every document.',
            'week' => 'This week, aim to submit or ask exactly what is missing.',
            'waiting' => 'If you are waiting, check the status and ask for the next date or person responsible.',
            default => 'If you are unsure, ask what would keep the application active.',
        };

        $blockerNote = $blocker !== '' ? " Name the blocker without apologizing: {$blocker}." : '';
        $deadlineNote = $deadline !== '' ? " Mention the deadline: {$deadline}." : '';

        return new AidApplicationPlan(
            firstAction: "Open one note for {$program} and write the contact, deadline, and one missing document. {$urgencyNote}{$blockerNote}",
            documents: array_slice($documents, 0, 5),
            script: "Hi {$contact}, I am trying to apply for {$program} and I want to make sure I do the next step correctly.{$deadlineNote} Could you tell me what is required, what can be submitted later, and where I should send it?",
            questions: [
                ...self::QUESTIONS[$category],
                'What happens if I miss or cannot meet the deadline?',
                'Can you confirm the next step in writing by email or message?',
            ],
            statusSteps: [
                'Save the office name, contact method, and date contacted.',
                'Write the next promised action in one sentence.',
                'Set a reminder for the next follow-up date.',
                'Keep proof of submission, screenshots, or confirmation numbers together.',
            ],
            stopRule: 'Stop after one contact attempt or one document found. The win is keeping the support path alive, not finishing the entire system today.',
        );
    }
}
