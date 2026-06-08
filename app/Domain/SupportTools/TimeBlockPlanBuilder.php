<?php

namespace App\Domain\SupportTools;

final class TimeBlockPlanBuilder
{
    /**
     * @var array<string, int>
     */
    private const BUFFER_MINUTES = [
        'light' => 10,
        'medium' => 15,
        'heavy' => 25,
    ];

    /**
     * @var array<string, int>
     */
    private const FOCUS_MINUTES = [
        'low' => 20,
        'medium' => 30,
        'high' => 45,
    ];

    public function build(TimeBlockInput $input): TimeBlockPlan
    {
        $start = $this->parseMinutes($input->startTime) ?? 540;
        $end = $this->parseMinutes($input->endTime) ?? 1020;

        if ($end <= $start) {
            $end = $start + 240;
        }

        $buffer = self::BUFFER_MINUTES[$input->bufferLevel] ?? self::BUFFER_MINUTES['medium'];
        $focus = self::FOCUS_MINUTES[$input->energy] ?? self::FOCUS_MINUTES['medium'];
        $tasks = $this->lines($input->mustDo);
        $anchors = $this->lines($input->fixedEvents);
        $recovery = $this->fallback($input->recovery, 'food, water, movement, medication routine, rest, or a quiet reset');
        $cursor = $start;

        $blocks = [];
        foreach (array_slice($tasks, 0, 5) as $task) {
            $taskEnd = min($cursor + $focus, $end);
            $blocks[] = $this->range($cursor, $taskEnd).": {$task}";
            $cursor = min($taskEnd + $buffer, $end);

            if ($cursor >= $end) {
                break;
            }
        }

        if ($blocks === []) {
            $taskEnd = min($cursor + $focus, $end);
            $blocks[] = $this->range($cursor, $taskEnd).': choose one must-do item and touch it for two minutes';
            $cursor = min($taskEnd + $buffer, $end);
        }

        return new TimeBlockPlan(
            window: $this->range($start, $end),
            blocks: $blocks,
            bufferRules: [
                "Add {$buffer} minutes after each focus block for transition, setup, notes, bathroom, or finding things.",
                'If a fixed event exists, protect the block before it as preparation rather than squeezing in a new task.',
                'Leave one blank block on purpose. Blank time is part of the plan, not a failure.',
            ],
            fixedAnchors: $anchors === [] ? ['No fixed events listed. Add appointments, calls, school/work times, pickup times, or deadlines if they exist.'] : $anchors,
            recoveryBlock: "Recovery block: {$recovery}. Put it before you crash, not only after.",
            fallbackRule: 'If the day slips, restart with the next visible block only. Do not rebuild the whole schedule while stressed.',
        );
    }

    private function parseMinutes(string $time): ?int
    {
        if (! preg_match('/^(\d{1,2}):(\d{2})$/', trim($time), $matches)) {
            return null;
        }

        $hours = (int) $matches[1];
        $minutes = (int) $matches[2];

        if ($hours > 23 || $minutes > 59) {
            return null;
        }

        return ($hours * 60) + $minutes;
    }

    /**
     * @return array<int, string>
     */
    private function lines(string $value): array
    {
        return array_values(array_filter(array_map(
            static fn (string $line): string => trim($line),
            preg_split('/\R/', $value) ?: [],
        )));
    }

    private function range(int $start, int $end): string
    {
        return $this->formatMinutes($start).' - '.$this->formatMinutes($end);
    }

    private function formatMinutes(int $minutes): string
    {
        $minutes = max(0, min($minutes, 1439));

        return sprintf('%02d:%02d', intdiv($minutes, 60), $minutes % 60);
    }

    private function fallback(string $value, string $fallback): string
    {
        $trimmed = trim($value);

        return $trimmed === '' ? $fallback : $trimmed;
    }
}
