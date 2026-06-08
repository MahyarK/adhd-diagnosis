<?php

namespace App\Domain\SupportTools;

final class LostItemPlanBuilder
{
    /**
     * @var array<string, string>
     */
    private const TYPE_HINTS = [
        'keys' => 'hooks, jacket pockets, bags, door areas, car seats, and the last lock you used',
        'wallet' => 'bags, coat pockets, desk surfaces, payment spots, laundry, and the car',
        'phone' => 'charger areas, bed/couch gaps, bathroom, kitchen, silent-mode places, and connected devices',
        'document' => 'mail piles, scanner/printer areas, bags, folders, desk edges, and photo/email copies',
        'medicine' => 'bedside table, kitchen, bathroom, bags, refill bag, and the last place you took it',
        'other' => 'the last used place, pockets, bags, surfaces at hand height, and any “I will put this here for a second” spots',
    ];

    public function build(LostItemInput $input): LostItemPlan
    {
        $item = $this->fallback($input->item, 'the missing item');
        $type = array_key_exists($input->type, self::TYPE_HINTS) ? $input->type : 'other';
        $lastSeen = $this->fallback($input->lastSeen, 'the last place you remember using it');
        $searchArea = $this->fallback($input->searchArea, 'one room or path');
        $landingSpot = $this->fallback($input->landingSpot, 'one visible landing spot near the door');
        $urgency = $this->urgencyLine($input->urgency);

        return new LostItemPlan(
            firstAction: "Stand still, take one slow breath, and name the target: “I am looking for {$item}.” Check your hands, pockets, bag, and the surface directly in front of you before moving rooms.",
            searchSteps: [
                "Search {$searchArea} in one direction for two minutes. Do not tidy unrelated things while searching.",
                "Replay the path from {$lastSeen} to now and touch each likely surface once.",
                "Check the high-probability places for this item: ".self::TYPE_HINTS[$type].'.',
                "Look in one odd-but-common ADHD spot: fridge shelf, bathroom counter, laundry, trash-safe surfaces, bed/couch gaps, or on top of another object.",
                $urgency,
            ],
            backupSteps: [
                "If it is not found after two search loops, pause and ask one person or system for help.",
                "For urgent items, switch to a backup: spare key, card freeze, call phone, reprint document, pharmacy/clinic call, or proof-of-identity workaround.",
                "Write where you already checked so you do not restart from zero.",
            ],
            preventionRule: "After this, give {$item} one home: {$landingSpot}. Make that spot easier than any random surface.",
            stopRule: 'Stop after two focused search loops or fifteen minutes. Escalate to backup steps instead of losing the whole day.',
        );
    }

    private function fallback(string $value, string $fallback): string
    {
        $trimmed = trim($value);

        return $trimmed === '' ? $fallback : $trimmed;
    }

    private function urgencyLine(string $urgency): string
    {
        return match ($urgency) {
            'leave' => 'Because you need to leave soon, set a five-minute timer and then switch to the backup plan.',
            'today' => 'Because it matters today, protect two search loops before doing lower-stakes tasks.',
            'important' => 'Because it is important but not immediate, record the search area and schedule one more search block.',
            default => 'Because this is a calm search, keep your body slow and your search path boringly systematic.',
        };
    }
}
