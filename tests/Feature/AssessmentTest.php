<?php

namespace Tests\Feature;

use Tests\TestCase;

class AssessmentTest extends TestCase
{
    public function test_home_page_loads_the_screening_app(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Gentle ADHD screening')
            ->assertSee('Find clinicians near you')
            ->assertSee('Score details')
            ->assertSee('assessment/score');
    }

    public function test_language_switcher_can_render_persian_rtl(): void
    {
        $this->get('/?lang=fa')
            ->assertOk()
            ->assertSee('dir="rtl"', false)
            ->assertSee('غربالگری آرام ADHD');
    }

    public function test_combined_presentation_can_be_scored(): void
    {
        $answers = $this->baseContext();

        foreach ([
            'ia_detail', 'ia_sustain', 'ia_listen', 'ia_finish', 'ia_organize',
            'hi_fidget', 'hi_leave', 'hi_restless', 'hi_quiet', 'hi_on_go',
        ] as $id) {
            $answers[$id] = 3;
        }

        $this->postJson('/assessment/score', ['answers' => $answers])
            ->assertOk()
            ->assertJsonPath('type', 'combined')
            ->assertJsonPath('counts.inattentive', 5)
            ->assertJsonPath('counts.hyperactive', 5)
            ->assertJsonPath('profile.plain_title', 'What combined presentation means');
    }

    public function test_symptoms_without_context_are_flagged_for_more_context(): void
    {
        $answers = array_merge($this->baseContext(), [
            'childhood' => 'no',
            'settings' => 'one',
        ]);

        foreach (['ia_detail', 'ia_sustain', 'ia_listen', 'ia_finish', 'ia_organize'] as $id) {
            $answers[$id] = 4;
        }

        $this->postJson('/assessment/score', ['answers' => $answers])
            ->assertOk()
            ->assertJsonPath('type', 'needs_context');
    }

    public function test_scoring_response_uses_selected_language(): void
    {
        $this->withSession(['locale' => 'fr'])
            ->postJson('/assessment/score', ['answers' => $this->baseContext()])
            ->assertOk()
            ->assertJsonPath('title', 'Le signal TDAH est plus faible dans ce dépistage');
    }

    public function test_result_profile_uses_selected_language(): void
    {
        $answers = $this->baseContext();

        foreach (['ia_detail', 'ia_sustain', 'ia_listen', 'ia_finish', 'ia_organize'] as $id) {
            $answers[$id] = 3;
        }

        $this->withSession(['locale' => 'fa'])
            ->postJson('/assessment/score', ['answers' => $answers])
            ->assertOk()
            ->assertJsonPath('title', 'الگوی غربالگری: ارائه عمدتاً بی‌توجه ADHD')
            ->assertJsonPath('profile.plain_title', 'ارائه بی‌توجه یعنی چه');
    }

    public function test_support_tool_pages_load(): void
    {
        $this->get('/dashboard?lang=en')
            ->assertOk()
            ->assertSee('Your local ADHD support kit.');

        $this->get('/resources?lang=en')
            ->assertOk()
            ->assertSee('Start with trustworthy help, not a research spiral.');

        $this->get('/tools/goal-builder?lang=en')
            ->assertOk()
            ->assertSee('Make one goal small enough to start.');

        $this->get('/tools/daily-planner?lang=nl')
            ->assertOk()
            ->assertSee('Maak een dagplan dat het echte leven overleeft.');

        $this->get('/tools/weekly-reset?lang=en')
            ->assertOk()
            ->assertSee('Reset the week without shame.');

        $this->get('/tools/appointment-prep?lang=fr')
            ->assertOk()
            ->assertSee('Rendre la demande d’aide plus facile.');

        $this->get('/tools/care-notes?lang=nl')
            ->assertOk()
            ->assertSee('Ga het gesprek in met je verhaal op orde.');

        $this->get('/tools/support-request?lang=fa')
            ->assertOk()
            ->assertSee('کمک بخواه بدون اینکه مجبور باشی همان لحظه کلمات را پیدا کنی.');

        $this->get('/tools/provider-shortlist?lang=en')
            ->assertOk()
            ->assertSee('Keep possible support options in one place.');

        $this->get('/tools/access-plan?lang=en')
            ->assertOk()
            ->assertSee('Make getting help less expensive and less vague.');

        $this->get('/tools/task-breakdown?lang=fa')
            ->assertOk()
            ->assertSee('یک کار سنگین را به قدم‌های کوچک و قابل دیدن تبدیل کن.');

        $this->get('/tools/reminders?lang=en')
            ->assertOk()
            ->assertSee('Make the next step harder to forget.');

        $this->get('/tools/symptom-tracker?lang=en')
            ->assertOk()
            ->assertSee('Track patterns without overthinking it.');
    }

    public function test_supported_translation_files_cover_result_and_tool_keys(): void
    {
        $english = require base_path('lang/en/assessment.php');

        foreach (['nl', 'fr', 'fa'] as $locale) {
            $translated = require base_path("lang/{$locale}/assessment.php");

            $this->assertTranslationKeys($english, $translated, $locale);
        }
    }

    private function baseContext(): array
    {
        return [
            'age_group' => 'adult',
            'childhood' => 'yes',
            'settings' => 'many',
            'impairment' => 'moderate',
            'duration' => 'six_months',
            'sleep' => 'no',
            'mood_anxiety' => 'no',
            'substances' => 'no',
            'medical' => 'no',
            'masking' => 'some',
            'family' => 'unknown',
            'support' => 'maybe',
        ];
    }

    private function assertTranslationKeys(array $expected, array $actual, string $locale, string $path = ''): void
    {
        foreach ($expected as $key => $value) {
            $currentPath = $path === '' ? (string) $key : "{$path}.{$key}";

            $this->assertArrayHasKey($key, $actual, "Missing {$currentPath} in {$locale} translations.");

            if (is_array($value)) {
                $this->assertIsArray($actual[$key], "Expected {$currentPath} in {$locale} to be an array.");
                $this->assertTranslationKeys($value, $actual[$key], $locale, $currentPath);
            }
        }
    }
}
