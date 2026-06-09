<?php

namespace App\Domain\SupportTools;

final class DigitalClutterPlanBuilder
{
    /**
     * @var array<string, array<int, string>>
     */
    private const MODE_STEPS = [
        'tabs' => [
            'Keep only the tab needed for the current target visible.',
            'Bookmark or copy any may-need-later links into one parking note.',
            'Close duplicate, finished, or panic-opened tabs without reading them again.',
        ],
        'messages' => [
            'Search for the one sender, subject, or keyword connected to the target.',
            'Open only the most recent relevant message first.',
            'Reply with a short “I am catching up” message if a perfect reply is blocking you.',
        ],
        'files' => [
            'Search downloads, desktop, and recent files for one keyword.',
            'Move the found item into one temporary “use next” folder or visible spot.',
            'If the file is not found in five minutes, write what it is called and who can resend it.',
        ],
        'notifications' => [
            'Turn off or pause non-urgent notifications for the next focus block.',
            'Check only the app tied to the target.',
            'Leave notification cleanup for later unless it protects today.',
        ],
        'overwhelm' => [
            'Choose one digital surface: tabs, inbox, files, or phone home screen.',
            'Make it easier to find the next action, not clean everything.',
            'Write one restart note before switching apps.',
        ],
    ];

    public function build(DigitalClutterInput $input): DigitalClutterPlan
    {
        $mode = array_key_exists($input->mode, self::MODE_STEPS) ? $input->mode : 'overwhelm';
        $target = $this->fallback($input->target, 'one digital next step');
        $deadline = $this->fallback($input->deadline, 'today');
        $blocker = $this->fallback($input->blocker, 'digital overwhelm');

        return new DigitalClutterPlan(
            firstAction: "Set a two-minute timer and open only the place most likely to contain {$target}.",
            steps: [
                ...self::MODE_STEPS[$mode],
                $this->deviceStep($input->device),
                $this->energyStep($input->energy),
                "Park the blocker in words instead of solving it now: {$blocker}.",
            ],
            searchRule: "Search rule: use one keyword, one place, and one timer. If it is not found, write the next person or system to ask.",
            shutdownRule: "Shutdown rule: before leaving the device, save one visible restart note for {$deadline}.",
            parkingRules: [
                'Later links, screenshots, and thoughts go into one parking note.',
                'Do not reorganize folders, labels, or the whole inbox during rescue mode.',
                'A messy but findable next step beats a perfect digital system.',
            ],
            stopRule: 'Stop when the target is visible, sent, saved, or written as a next ask. Digital rescue is not an inbox-cleaning marathon.',
        );
    }

    private function deviceStep(string $device): string
    {
        return match ($device) {
            'phone' => 'On phone: move the needed app to the first screen or use search instead of scrolling.',
            'computer' => 'On computer: use browser history, downloads, recent files, or app search before manually browsing folders.',
            'both' => 'Across devices: check the device you used most recently first, then stop before bouncing back and forth.',
            default => 'Use the device with the lowest friction right now.',
        };
    }

    private function energyStep(string $energy): string
    {
        return match ($energy) {
            'none' => 'No-energy version: write the target and close or lock the device after one visible restart note.',
            'low' => 'Low-energy version: rescue one target only, then stop.',
            'some' => 'Some-energy version: rescue the target, then clear only the digital surface that blocks restarting.',
            default => 'Choose the smallest digital action that protects the next step.',
        };
    }

    private function fallback(string $value, string $fallback): string
    {
        $trimmed = trim($value);

        return $trimmed === '' ? $fallback : $trimmed;
    }
}
