<?php

namespace App\Domain\SupportTools;

final class SupportNavigatorPlanBuilder
{
    /**
     * @var array<string, array<int, string>>
     */
    private const TOPIC_ROUTES = [
        'task' => ['task', 'focus', 'motivation'],
        'body' => ['body', 'food', 'sleep'],
        'money' => ['money', 'aid', 'followup', 'reminders'],
        'home' => ['home', 'laundry', 'lost'],
        'care' => ['followup', 'appointment', 'care', 'providers'],
        'emotional' => ['safety', 'emotion', 'wins', 'energy'],
        'workschool' => ['job', 'workschool', 'support', 'communication'],
        'leaving' => ['errand', 'transition', 'lost'],
        'unsure' => ['task', 'body', 'decision'],
    ];

    public function build(SupportNavigatorInput $input): SupportNavigatorPlan
    {
        $topic = array_key_exists($input->topic, self::TOPIC_ROUTES) ? $input->topic : 'unsure';
        $recommendations = self::TOPIC_ROUTES[$topic];
        $knownNext = trim($input->knownNext);

        if ($knownNext !== '') {
            array_unshift($recommendations, 'planner');
            $recommendations = array_values(array_unique($recommendations));
        }

        if ($input->urgency === 'now' && ! in_array('transition', $recommendations, true)) {
            array_splice($recommendations, 1, 0, 'transition');
            $recommendations = array_values(array_unique($recommendations));
        }

        if ($input->energy === 'low' && ! in_array('energy', $recommendations, true)) {
            $recommendations[] = 'energy';
        }

        return new SupportNavigatorPlan(
            firstAction: $knownNext === ''
                ? 'Pick the first recommended card and spend two minutes opening it.'
                : "Write this as the next tiny action: {$knownNext}. Then choose the first recommended card.",
            recommendations: array_slice($recommendations, 0, 4),
            why: 'The navigator chooses a small tool path from the current problem, energy level, and urgency so the user does not have to browse the whole hub while overwhelmed.',
            stopRule: 'Stop after choosing one tool. Starting the right worksheet is enough progress for this round.',
        );
    }
}
