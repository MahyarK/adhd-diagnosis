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

        $this->get('/tools/time-block?lang=fr')
            ->assertOk()
            ->assertSee('Faites une carte de journée réaliste, pas un planning impossible.');

        $this->get('/tools/body-needs?lang=fa')
            ->assertOk()
            ->assertSee('قبل از سرزنش مغزت، پایه‌ها را چک کن.');

        $this->get('/tools/food-rescue?lang=fr')
            ->assertOk()
            ->assertSee('Mangez assez pour pouvoir continuer.');

        $this->get('/tools/sleep-wind-down?lang=en')
            ->assertOk()
            ->assertSee('Get closer to sleep without a perfect night routine.');

        $this->get('/tools/motivation-menu?lang=en')
            ->assertOk()
            ->assertSee('Build a free reward menu for boring tasks.');

        $this->get('/tools/accountability?lang=fr')
            ->assertOk()
            ->assertSee('Demandez à quelqu’un de témoigner du départ, pas de surveiller la fin.');

        $this->get('/tools/routine-builder?lang=fa')
            ->assertOk()
            ->assertSee('روتینی بساز که در روزهای ناقص هم کار کند.');

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

        $this->get('/tools/home-reset?lang=nl')
            ->assertOk()
            ->assertSee('Reset één plek zonder er een levensreorganisatie van te maken.');

        $this->get('/tools/laundry-rescue?lang=fr')
            ->assertOk()
            ->assertSee('Protéger d’abord la prochaine tenue portable.');

        $this->get('/tools/digital-clutter?lang=nl')
            ->assertOk()
            ->assertSee('Vind de volgende digitale stap zonder het hele internet op te ruimen.');

        $this->get('/tools/money-admin?lang=en')
            ->assertOk()
            ->assertSee('Unstick one bill, form, message, or overdue admin task.');

        $this->get('/tools/lost-item?lang=nl')
            ->assertOk()
            ->assertSee('Vind één kwijtgeraakt ding zonder de hele dag te verliezen.');

        $this->get('/tools/communication-repair?lang=nl')
            ->assertOk()
            ->assertSee('Stuur het bericht zonder het de hele dag te herschrijven.');

        $this->get('/tools/energy-crash?lang=fr')
            ->assertOk()
            ->assertSee('Créez un plan basse capacité pour la journée que vous avez vraiment.');

        $this->get('/tools/decision-priority?lang=nl')
            ->assertOk()
            ->assertSee('Kies wat eerst komt wanneer alles even dringend voelt.');

        $this->get('/tools/focus-sprint?lang=fa')
            ->assertOk()
            ->assertSee('بدون منتظر ماندن برای انگیزه، یک اسپرینت کاری کوچک شروع کن.');

        $this->get('/tools/emotional-reset?lang=en')
            ->assertOk()
            ->assertSee('Climb out of a shame spiral one gentle step at a time.');

        $this->get('/tools/transition-rescue?lang=nl')
            ->assertOk()
            ->assertSee('Kom uit wachtstand en naar de volgende beweging.');

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
