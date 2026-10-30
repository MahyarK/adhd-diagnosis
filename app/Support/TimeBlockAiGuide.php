<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;
use Throwable;

final class TimeBlockAiGuide
{
    /**
     * @param  array{start?: string|null, end?: string|null, must?: string|null, fixed?: string|null, buffer?: string|null, energy?: string|null, recovery?: string|null, locale: string}  $input
     * @return array{available: bool, title: string, blocks: array<int, string>, fixed: array<int, string>, buffers: array<int, string>, recovery: string, fallback: string}
     */
    public function guide(array $input): array
    {
        $key = config('services.openai.key');

        if (! is_string($key) || trim($key) === '') {
            return $this->fallback($input);
        }

        try {
            $response = Http::withToken($key)
                ->timeout(12)
                ->acceptJson()
                ->post('https://api.openai.com/v1/responses', [
                    'model' => config('services.openai.model'),
                    'input' => [
                        [
                            'role' => 'system',
                            'content' => $this->systemPrompt(),
                        ],
                        [
                            'role' => 'user',
                            'content' => json_encode($input, JSON_THROW_ON_ERROR),
                        ],
                    ],
                    'text' => [
                        'format' => [
                            'type' => 'json_schema',
                            'name' => 'adhd_time_blocks',
                            'schema' => [
                                'type' => 'object',
                                'additionalProperties' => false,
                                'properties' => [
                                    'title' => ['type' => 'string'],
                                    'blocks' => [
                                        'type' => 'array',
                                        'items' => ['type' => 'string'],
                                        'minItems' => 1,
                                        'maxItems' => 6,
                                    ],
                                    'fixed' => [
                                        'type' => 'array',
                                        'items' => ['type' => 'string'],
                                        'minItems' => 1,
                                        'maxItems' => 5,
                                    ],
                                    'buffers' => [
                                        'type' => 'array',
                                        'items' => ['type' => 'string'],
                                        'minItems' => 2,
                                        'maxItems' => 4,
                                    ],
                                    'recovery' => ['type' => 'string'],
                                    'fallback' => ['type' => 'string'],
                                ],
                                'required' => ['title', 'blocks', 'fixed', 'buffers', 'recovery', 'fallback'],
                            ],
                        ],
                    ],
                ]);

            if (! $response->successful()) {
                return $this->fallback($input);
            }

            $text = $response->json('output_text');
            $decoded = is_string($text) ? json_decode($text, true) : null;

            if (! is_array($decoded)) {
                return $this->fallback($input);
            }

            $plan = [
                'available' => true,
                'title' => $this->clean($decoded['title'] ?? ''),
                'blocks' => $this->cleanList($decoded['blocks'] ?? []),
                'fixed' => $this->cleanList($decoded['fixed'] ?? []),
                'buffers' => $this->cleanList($decoded['buffers'] ?? []),
                'recovery' => $this->clean($decoded['recovery'] ?? ''),
                'fallback' => $this->clean($decoded['fallback'] ?? ''),
            ];

            if ($plan['title'] === '' || $plan['blocks'] === [] || $plan['buffers'] === [] || $plan['recovery'] === '' || $plan['fallback'] === '') {
                return $this->fallback($input);
            }

            return $plan;
        } catch (Throwable) {
            return $this->fallback($input);
        }
    }

    private function systemPrompt(): string
    {
        return implode(' ', [
            'You are an ADHD-friendly time-block planning assistant inside a self-help app.',
            'Return JSON only with title, blocks, fixed, buffers, recovery, and fallback.',
            'Use the user locale if possible.',
            'Make a realistic buffered map, not a packed schedule.',
            'Do not diagnose, do not mention being an AI, and do not make medical claims.',
            'Use short focus blocks, visible transition buffers, and one intentional blank or recovery space.',
            'Treat fixed events as anchors that need preparation time.',
            'Keep each item under 22 words.',
        ]);
    }

    /**
     * @param  array{start?: string|null, end?: string|null, must?: string|null, fixed?: string|null, buffer?: string|null, energy?: string|null, recovery?: string|null, locale: string}  $input
     * @return array{available: bool, title: string, blocks: array<int, string>, fixed: array<int, string>, buffers: array<int, string>, recovery: string, fallback: string}
     */
    private function fallback(array $input): array
    {
        $start = $this->clock($input['start'] ?? '') ?: '09:00';
        $end = $this->clock($input['end'] ?? '') ?: '17:00';
        $must = $this->lines($input['must'] ?? '');
        $fixed = $this->lines($input['fixed'] ?? '');
        $recovery = $this->clean($input['recovery'] ?? '');
        $focusMinutes = match ($this->clean($input['energy'] ?? 'medium')) {
            'low' => 20,
            'high' => 45,
            default => 30,
        };
        $bufferMinutes = match ($this->clean($input['buffer'] ?? 'medium')) {
            'light' => 10,
            'heavy' => 25,
            default => 15,
        };

        return [
            'available' => false,
            'title' => __('assessment.tools.time.ai_fallback.title', ['start' => $start, 'end' => $end]),
            'blocks' => $this->fallbackBlocks($start, $must, $focusMinutes, $bufferMinutes),
            'fixed' => $fixed !== [] ? $fixed : [__('assessment.tools.time.ai_fallback.fixed')],
            'buffers' => [
                __('assessment.tools.time.ai_fallback.buffer_after', ['minutes' => $bufferMinutes]),
                __('assessment.tools.time.ai_fallback.buffer_blank'),
                __('assessment.tools.time.ai_fallback.buffer_fixed'),
            ],
            'recovery' => __('assessment.tools.time.ai_fallback.recovery', [
                'recovery' => $recovery !== '' ? $recovery : __('assessment.tools.time.ai_fallback.recovery_default'),
            ]),
            'fallback' => __('assessment.tools.time.ai_fallback.fallback'),
        ];
    }

    /**
     * @param  array<int, string>  $tasks
     * @return array<int, string>
     */
    private function fallbackBlocks(string $start, array $tasks, int $focusMinutes, int $bufferMinutes): array
    {
        $tasks = array_slice($tasks !== [] ? $tasks : [__('assessment.tools.time.ai_fallback.must')], 0, 4);
        [$hour, $minute] = array_map('intval', explode(':', $start));
        $cursor = $hour * 60 + $minute;

        return array_map(function (string $task) use (&$cursor, $focusMinutes, $bufferMinutes): string {
            $blockStart = $this->format($cursor);
            $cursor += $focusMinutes;
            $blockEnd = $this->format($cursor);
            $cursor += $bufferMinutes;

            return "{$blockStart} - {$blockEnd}: {$task}";
        }, $tasks);
    }

    /**
     * @return array<int, string>
     */
    private function lines(mixed $value): array
    {
        $text = $this->clean($value);

        if ($text === '') {
            return [];
        }

        return array_values(array_filter(array_map(
            fn (string $line): string => trim($line),
            preg_split('/\R/', $text) ?: [],
        )));
    }

    /**
     * @return array<int, string>
     */
    private function cleanList(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }

        return array_values(array_filter(array_map(fn (mixed $item): string => $this->clean($item), $value)));
    }

    private function clock(mixed $value): ?string
    {
        $text = $this->clean($value);

        return preg_match('/^\d{2}:\d{2}$/', $text) === 1 ? $text : null;
    }

    private function format(int $minutes): string
    {
        $minutes = max(0, $minutes);

        return sprintf('%02d:%02d', intdiv($minutes, 60) % 24, $minutes % 60);
    }

    private function clean(mixed $value): string
    {
        return trim(is_string($value) ? $value : '');
    }
}
