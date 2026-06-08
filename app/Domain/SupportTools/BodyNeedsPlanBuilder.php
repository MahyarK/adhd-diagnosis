<?php

namespace App\Domain\SupportTools;

final class BodyNeedsPlanBuilder
{
    /**
     * @var array<string, string>
     */
    private const FIRST_ACTIONS = [
        'empty' => 'Eat the easiest available food with protein, fat, or slow carbs. Good enough counts.',
        'snack' => 'Add water and one simple snack before asking your brain for more effort.',
        'okay' => 'Keep food steady: plan the next simple meal or snack now.',
        'unknown' => 'Check food first: if you cannot remember eating, choose a simple snack.',
    ];

    /**
     * @var array<string, string>
     */
    private const BODY_SUPPORT = [
        'tense' => 'Unclench jaw/shoulders, exhale slowly, and make the next task physically smaller.',
        'restless' => 'Move for two minutes before sitting still again.',
        'foggy' => 'Use light, water, and one written next step instead of relying on working memory.',
        'pain' => 'Lower demands and choose the least harmful next step for your body.',
        'unknown' => 'Scan head, chest, stomach, muscles, temperature, and bathroom need without judging the answer.',
    ];

    public function build(BodyNeedsInput $input): BodyNeedsPlan
    {
        $food = array_key_exists($input->food, self::FIRST_ACTIONS) ? $input->food : 'unknown';
        $bodySignal = array_key_exists($input->bodySignal, self::BODY_SUPPORT) ? $input->bodySignal : 'unknown';
        $nextTask = $this->fallback($input->nextTask, 'the next task');

        return new BodyNeedsPlan(
            firstAction: self::FIRST_ACTIONS[$food],
            supportSteps: [
                $this->waterStep($input->water),
                $this->medsStep($input->meds),
                $this->sleepStep($input->sleep),
                self::BODY_SUPPORT[$bodySignal],
            ],
            checks: [
                'Food: do I need something easy before effort?',
                'Water: can I put water where my hand will find it?',
                'Medication routine: do I need to check the normal instruction or log?',
                'Body: do I need bathroom, warmth/cooling, movement, pain support, or rest?',
            ],
            taskAdjustment: "Adjust {$nextTask}: make it two minutes, seated/standing as needed, and stop before body signals become louder.",
            stopRule: 'Stop the check-in when one body need is handled and the next task has been made smaller. You do not have to fix every need before restarting.',
        );
    }

    private function waterStep(string $water): string
    {
        return match ($water) {
            'low' => 'Drink water now or place it beside the next task.',
            'some' => 'Keep water visible and take a few sips before switching tasks.',
            'okay' => 'Water is probably okay; keep it reachable.',
            default => 'If you are unsure, put water in sight and take a few sips.',
        };
    }

    private function medsStep(string $meds): string
    {
        return match ($meds) {
            'needed' => 'Check your prescribed medication routine or reminder before continuing.',
            'done' => 'Medication routine is marked done; do not re-check repeatedly unless needed for safety.',
            'none' => 'No medication step selected; skip this without guilt.',
            default => 'If medication applies, check only the normal instruction, reminder, or log.',
        };
    }

    private function sleepStep(string $sleep): string
    {
        return match ($sleep) {
            'low' => 'Treat today as low capacity: shorter blocks, more buffer, fewer promises.',
            'medium' => 'Use normal blocks, but add a recovery pause before the hard task.',
            'okay' => 'Sleep seems okay; still protect one recovery pause.',
            default => 'Assume capacity may be mixed and choose the smaller version first.',
        };
    }

    private function fallback(string $value, string $fallback): string
    {
        $trimmed = trim($value);

        return $trimmed === '' ? $fallback : $trimmed;
    }
}
