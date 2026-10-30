<?php

namespace App\Support;

use Illuminate\Support\Facades\Route;

class Toolkit
{
    /**
     * Calm grouping of every support tool by what the person needs, keyed by dashboard card key.
     */
    public const CATEGORIES = [
        'start' => ['checkin', 'navigator', 'planner', 'time', 'task', 'goal', 'focus', 'decision', 'routine', 'weekly', 'transition'],
        'body' => ['body', 'meds', 'food', 'sleep', 'energy'],
        'feelings' => ['emotion', 'safety', 'motivation', 'wins', 'accountability', 'communication'],
        'life' => ['home', 'laundry', 'digital', 'lost', 'errand', 'money', 'aid', 'followup', 'reminders'],
        'care' => ['appointment', 'care', 'providers', 'access', 'tracker', 'support', 'workschool', 'job'],
    ];

    /**
     * @return array<string, array{key: string, label: string, body: string, tools: array<int, array{key: string, title: string, body: string, href: string}>}>
     */
    public static function groups(string $locale): array
    {
        $cards = __('assessment.tools.dashboard.cards');
        $copy = __('assessment.tools.toolkit.categories');
        $groups = [];

        foreach (self::CATEGORIES as $category => $keys) {
            $groups[$category] = [
                'key' => $category,
                'label' => $copy[$category]['label'] ?? $category,
                'body' => $copy[$category]['body'] ?? '',
                'tools' => array_values(array_filter(array_map(
                    fn (string $key): ?array => isset($cards[$key]) && Route::has($cards[$key]['route'])
                        ? [
                            'key' => $key,
                            'title' => $cards[$key]['title'],
                            'body' => $cards[$key]['empty'],
                            'href' => route($cards[$key]['route'], ['lang' => $locale]),
                        ]
                        : null,
                    $keys,
                ))),
            ];
        }

        return $groups;
    }

    /**
     * The category and sibling tools for the tool page being viewed, or null outside tool pages.
     *
     * @return array{category: array, tool: array, related: array}|null
     */
    public static function context(?string $routeName, string $locale): ?array
    {
        if (! $routeName || ! str_starts_with($routeName, 'tools.')) {
            return null;
        }

        foreach (self::groups($locale) as $group) {
            foreach ($group['tools'] as $tool) {
                if ($tool['href'] === route($routeName, ['lang' => $locale])) {
                    return [
                        'category' => $group,
                        'tool' => $tool,
                        'related' => array_values(array_filter($group['tools'], fn (array $other): bool => $other['key'] !== $tool['key'])),
                    ];
                }
            }
        }

        return null;
    }
}
