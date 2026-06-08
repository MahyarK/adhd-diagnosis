<?php

namespace App\Domain\SupportTools;

final class FoodRescuePlanBuilder
{
    /**
     * @var array<string, array<int, string>>
     */
    private const OPTIONS = [
        'none' => [
            'Drink water or something with calories, then eat any safe ready-to-eat item.',
            'Make a snack plate: crackers or bread, protein if available, fruit/veg if available.',
            'Use the easiest backup: yogurt, cereal, soup, leftovers, sandwich, nuts, or delivery if safe for budget.',
        ],
        'low' => [
            'Choose the lowest-step meal: toast, eggs, noodles, rice bowl, canned soup, frozen meal, or leftovers.',
            'Add one protein or fat if possible: beans, egg, cheese, peanut butter, tuna, tofu, yogurt, nuts, or hummus.',
            'Put food where you will see it and sit down before opening another app.',
        ],
        'some' => [
            'Make one “good enough” plate with carb, protein/fat, and any color or fiber you can manage.',
            'Cook only one base, then add ready toppings.',
            'Prepare one extra portion only if it does not stop you from eating now.',
        ],
    ];

    /**
     * @var array<string, array<int, string>>
     */
    private const SHOPPING = [
        'free' => [
            'Use what is already at home first.',
            'Check pantry, freezer, school/work food shelf, community fridge, or someone safe before spending.',
            'Put one shelf-stable backup on the list for later if possible.',
        ],
        'very_low' => [
            'Bread, rice, oats, pasta, or potatoes.',
            'Beans, eggs, peanut butter, lentils, tuna, tofu, yogurt, or cheese.',
            'Bananas, frozen vegetables, canned tomatoes, soup, or one easy fruit/veg.',
        ],
        'flexible' => [
            'One ready meal or frozen option for emergency days.',
            'One protein you will actually eat.',
            'One snack that helps you start eating when appetite is low.',
        ],
    ];

    public function build(FoodRescueInput $input): FoodRescuePlan
    {
        $energy = array_key_exists($input->energy, self::OPTIONS) ? $input->energy : 'low';
        $budget = array_key_exists($input->budget, self::SHOPPING) ? $input->budget : 'very_low';
        $available = $this->fallback($input->available, 'whatever is easiest and safe to eat');
        $nextThing = $this->fallback($input->nextThing, 'the next task');

        return new FoodRescuePlan(
            firstAction: $this->firstAction($input->appetite, $input->kitchen, $available),
            options: self::OPTIONS[$energy],
            shoppingList: self::SHOPPING[$budget],
            nextCue: "After eating, do not restart the whole day. Set a ten-minute timer, then begin {$nextThing} at the smallest visible step.",
            setupRules: [
                'Good enough food counts. This is a rescue, not a performance.',
                'Reduce steps before improving the meal.',
                'Keep one emergency food where you can see it.',
            ],
            stopRule: 'Stop when you have eaten or set up the next easiest bite. Avoid turning food rescue into a full kitchen reset.',
        );
    }

    private function firstAction(string $appetite, string $kitchen, string $available): string
    {
        $appetiteLine = match ($appetite) {
            'low' => 'Start with three bites or a drink with calories.',
            'forgot' => 'Treat this as a body maintenance task, not a motivation task.',
            'hungry' => 'Eat the fastest safe option before making a better plan.',
            default => 'Pick the easiest option before comparing every option.',
        };

        $kitchenLine = match ($kitchen) {
            'none' => 'Use no-cook food or leave the room to get food if needed.',
            'microwave' => 'Use the microwave or kettle before anything with more steps.',
            'stove' => 'Use the stove only if it still feels easier than not eating.',
            default => 'Use the easiest available setup.',
        };

        return "{$appetiteLine} {$kitchenLine} Start with: {$available}.";
    }

    private function fallback(string $value, string $fallback): string
    {
        $trimmed = trim($value);

        return $trimmed === '' ? $fallback : $trimmed;
    }
}
