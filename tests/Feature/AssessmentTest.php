<?php

namespace Tests\Feature;

use App\Support\Toolkit;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AssessmentTest extends TestCase
{
    public function test_home_page_starts_with_today(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('How is your day feeling?')
            ->assertSee('Start with today')
            ->assertSee('Tell the app how today feels.')
            ->assertSee('Saved support')
            ->assertSee('/tools/daily-check-in?lang=en', false);
    }

    public function test_assessment_page_loads_the_screening_app(): void
    {
        $this->get('/assessment?lang=en')
            ->assertOk()
            ->assertSee('Gentle ADHD screening')
            ->assertSee('Find clinicians near you')
            ->assertSee('Score details')
            ->assertSee('Before you begin')
            ->assertSee('Browse all tools')
            ->assertSee('assessment/score');
    }

    public function test_toolkit_groups_every_tool_and_tool_pages_link_back(): void
    {
        $this->get('/tools?lang=en')
            ->assertOk()
            ->assertSee('Every tool, sorted by what you need.')
            ->assertSee('Body and energy')
            ->assertSee('/tools/laundry-rescue?lang=en', false)
            ->assertSee('/tools/symptom-tracker?lang=en', false);

        $this->get('/tools?lang=fa')
            ->assertOk()
            ->assertSee('همهٔ ابزارها، مرتب‌شده بر اساس چیزی که لازم داری.');

        $this->get('/tools/laundry-rescue?lang=en')
            ->assertOk()
            ->assertSee('Home, money and errands')
            ->assertSee('More in Home, money and errands');
    }

    public function test_toolkit_covers_every_tool_route_once(): void
    {
        $grouped = collect(Toolkit::CATEGORIES)->flatten();
        $toolCards = collect(trans('assessment.tools.dashboard.cards', [], 'en'))->except(['result', 'resources'])->keys();

        $this->assertSame($grouped->count(), $grouped->unique()->count());
        $this->assertEqualsCanonicalizing($toolCards->all(), $grouped->all());
    }

    public function test_language_switcher_can_render_persian_rtl(): void
    {
        $this->get('/?lang=fa')
            ->assertOk()
            ->assertSee('dir="rtl"', false)
            ->assertSee('امروزت چه حسی دارد؟');
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
            ->assertSee('How is your day feeling?')
            ->assertSee('Let’s find one kind next step.')
            ->assertSee('Pick the closest problem.')
            ->assertSee('No check-in saved yet')
            ->assertSee('After you check in, this space will show one tiny action and a few matched tools.')
            ->assertSee('Ask for one gentle next step.')
            ->assertSee('Optional AI sends only this message and your saved check-in context when you press the button.')
            ->assertSee('Open this when you want your notes, exports, and the fuller support library.')
            ->assertSee('Download notes')
            ->assertSee('Download data backup')
            ->assertSee('/tools/daily-check-in?lang=en', false)
            ->assertSee('/tools/money-admin?lang=en', false);

        $this->get('/resources?lang=en')
            ->assertOk()
            ->assertSee('Start with trustworthy help, not a research spiral.');

        $this->get('/tools/support-navigator?lang=fa')
            ->assertOk()
            ->assertSee('به هاب بگو الان چه چیزی سخت است.');

        $this->get('/tools/daily-check-in?lang=en')
            ->assertOk()
            ->assertSee('A calmer way to start today.')
            ->assertSee('Question :current of :total')
            ->assertSee('Feeling')
            ->assertSee('Next')
            ->assertSee('Ask AI to make it more natural')
            ->assertSee('AI is optional. It sends only this check-in to the server when you press the AI button.')
            ->assertSee('AI-ready note');

        $this->get('/tools/goal-builder?lang=en')
            ->assertOk()
            ->assertSee('Make one goal small enough to start.')
            ->assertSee('Ask AI to shrink it');

        $this->get('/tools/daily-planner?lang=nl')
            ->assertOk()
            ->assertSee('Maak een dagplan dat het echte leven overleeft.')
            ->assertSee('Vraag AI om het realistischer te maken');

        $this->get('/tools/time-block?lang=fr')
            ->assertOk()
            ->assertSee('Faites une carte de journée réaliste, pas un planning impossible.')
            ->assertSee('Demander à l’IA d’ajouter des marges');

        $this->get('/tools/body-needs?lang=fa')
            ->assertOk()
            ->assertSee('قبل از سرزنش مغزت، پایه‌ها را چک کن.')
            ->assertSee('از AI بخواه این را سازگار کند');

        $this->get('/tools/food-rescue?lang=fr')
            ->assertOk()
            ->assertSee('Mangez assez pour pouvoir continuer.');

        $this->get('/tools/sleep-wind-down?lang=en')
            ->assertOk()
            ->assertSee('Get closer to sleep without a perfect night routine.');

        $this->get('/tools/motivation-menu?lang=en')
            ->assertOk()
            ->assertSee('Build a free reward menu for boring tasks.');

        $this->get('/tools/wins-log?lang=fa')
            ->assertOk()
            ->assertSee('مدرک نگه دار که چیزی کار کرده، حتی اگر خیلی کوچک بوده.');

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
            ->assertSee('Rendre la demande d’aide plus facile.')
            ->assertSee('Questions coût et accès');

        $this->get('/tools/care-notes?lang=nl')
            ->assertOk()
            ->assertSee('Ga het gesprek in met je verhaal op orde.');

        $this->get('/tools/support-request?lang=fa')
            ->assertOk()
            ->assertSee('کمک بخواه بدون اینکه مجبور باشی همان لحظه کلمات را پیدا کنی.');

        $this->get('/tools/work-school-support?lang=nl')
            ->assertOk()
            ->assertSee('Kies één praktische steun om te testen, geen hele nieuwe persoonlijkheid.');

        $this->get('/tools/job-hunt?lang=en')
            ->assertOk()
            ->assertSee('Move one job or income step without rebuilding your whole life.');

        $this->get('/tools/home-reset?lang=nl')
            ->assertOk()
            ->assertSee('Reset één plek zonder er een levensreorganisatie van te maken.');

        $this->get('/tools/laundry-rescue?lang=fr')
            ->assertOk()
            ->assertSee('Protéger d’abord la prochaine tenue portable.');

        $this->get('/tools/medication-refill?lang=fa')
            ->assertOk()
            ->assertSee('از فاصله افتادن دارو جلوگیری کن بدون اینکه حدس بزنی چه کار کنی.')
            ->assertSee('از AI بخواه تمدید را مرتب کند');

        $this->get('/tools/digital-clutter?lang=nl')
            ->assertOk()
            ->assertSee('Vind de volgende digitale stap zonder het hele internet op te ruimen.');

        $this->get('/tools/money-admin?lang=en')
            ->assertOk()
            ->assertSee('Unstick one bill, form, message, or overdue admin task.')
            ->assertSee('Ask AI to unstick this');

        $this->get('/tools/aid-application?lang=fr')
            ->assertOk()
            ->assertSee('Faire avancer les dossiers d’aide quand l’argent, les formulaires ou la honte pèsent lourd.');

        $this->get('/tools/lost-item?lang=nl')
            ->assertOk()
            ->assertSee('Vind één kwijtgeraakt ding zonder de hele dag te verliezen.');

        $this->get('/tools/errand-launch?lang=fr')
            ->assertOk()
            ->assertSee('Sortir ou prendre contact sans reconstruire toute la journée.');

        $this->get('/tools/communication-repair?lang=nl')
            ->assertOk()
            ->assertSee('Stuur het bericht zonder het de hele dag te herschrijven.');

        $this->get('/tools/energy-crash?lang=fr')
            ->assertOk()
            ->assertSee('Créez un plan basse capacité pour la journée que vous avez vraiment.')
            ->assertSee('Demander à l’IA de réduire la journée');

        $this->get('/tools/decision-priority?lang=nl')
            ->assertOk()
            ->assertSee('Kies wat eerst komt wanneer alles even dringend voelt.')
            ->assertSee('Vraag AI om zacht te kiezen');

        $this->get('/tools/focus-sprint?lang=fa')
            ->assertOk()
            ->assertSee('بدون منتظر ماندن برای انگیزه، یک اسپرینت کاری کوچک شروع کن.')
            ->assertSee('از AI بخواه اسپرینت را شکل بدهد');

        $this->get('/tools/emotional-reset?lang=en')
            ->assertOk()
            ->assertSee('Climb out of a shame spiral one gentle step at a time.')
            ->assertSee('Ask AI to soften this');

        $this->get('/tools/safety-pause?lang=fr')
            ->assertOk()
            ->assertSee('Ralentir le moment et choisir le bon type d’aide.');

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
            ->assertSee('یک کار سنگین را به قدم‌های کوچک و قابل دیدن تبدیل کن.')
            ->assertSee('از AI بخواه آسان‌ترش کند')
            ->assertSee('کارهای اضافه');

        $this->get('/tools/follow-up-rescue?lang=nl')
            ->assertOk()
            ->assertSee('Pak één vergeten follow-up op zonder schaamtespiraal.');

        $this->get('/tools/reminders?lang=en')
            ->assertOk()
            ->assertSee('Make the next step harder to forget.');

        $this->get('/tools/symptom-tracker?lang=en')
            ->assertOk()
            ->assertSee('Track patterns without overthinking it.');
    }

    public function test_daily_check_in_ai_falls_back_when_not_configured(): void
    {
        config(['services.openai.key' => null]);

        $this->postJson('/tools/daily-check-in/ai?lang=en', [
            'feeling' => 'overwhelmed',
            'energy' => 'low',
            'pressure' => 'task',
            'message' => 'I cannot start',
        ])
            ->assertOk()
            ->assertJsonPath('available', false)
            ->assertJsonPath('response', 'AI is not configured yet. The local check-in still gives you a private, useful next step.');
    }

    public function test_daily_check_in_ai_returns_configured_guidance(): void
    {
        config([
            'services.openai.key' => 'test-key',
            'services.openai.model' => 'test-model',
        ]);

        Http::fake([
            'https://api.openai.com/v1/responses' => Http::response([
                'output_text' => json_encode([
                    'response' => 'That sounds heavy, so we will make it smaller and kinder.',
                    'first' => 'Open the bill and look only for the due date.',
                ]),
            ]),
        ]);

        $this->postJson('/tools/daily-check-in/ai?lang=en', [
            'feeling' => 'avoidant',
            'energy' => 'medium',
            'pressure' => 'money',
            'message' => 'I avoided a bill',
        ])
            ->assertOk()
            ->assertJsonPath('available', true)
            ->assertJsonPath('response', 'That sounds heavy, so we will make it smaller and kinder.')
            ->assertJsonPath('first', 'Open the bill and look only for the due date.');

        Http::assertSentCount(1);
    }

    public function test_dashboard_ai_falls_back_when_not_configured(): void
    {
        config(['services.openai.key' => null]);

        $this->postJson('/dashboard/ai?lang=en', [
            'message' => 'I avoided a bill and need help',
            'checkin' => [
                'feeling' => 'avoidant',
                'energy' => 'medium',
                'pressure' => 'money',
            ],
        ])
            ->assertOk()
            ->assertJsonPath('available', false)
            ->assertJsonPath('tool', 'money')
            ->assertJsonPath('response', 'AI is not configured yet, so here is the local guide: name the pressure, lower the task size, and choose one support route.');
    }

    public function test_dashboard_ai_returns_configured_guidance(): void
    {
        config([
            'services.openai.key' => 'test-key',
            'services.openai.model' => 'test-model',
        ]);

        Http::fake([
            'https://api.openai.com/v1/responses' => Http::response([
                'output_text' => json_encode([
                    'response' => 'You are not behind as a person; this is one recoverable admin loop.',
                    'first' => 'Open the bill and find the amount.',
                    'tool' => 'money',
                ]),
            ]),
        ]);

        $this->postJson('/dashboard/ai?lang=en', [
            'message' => 'I avoided a bill and need help',
            'checkin' => [
                'feeling' => 'avoidant',
                'energy' => 'medium',
                'pressure' => 'money',
            ],
        ])
            ->assertOk()
            ->assertJsonPath('available', true)
            ->assertJsonPath('response', 'You are not behind as a person; this is one recoverable admin loop.')
            ->assertJsonPath('first', 'Open the bill and find the amount.')
            ->assertJsonPath('tool', 'money');

        Http::assertSentCount(1);
    }

    public function test_task_breakdown_ai_falls_back_when_not_configured(): void
    {
        config(['services.openai.key' => null]);

        $this->postJson('/tools/task-breakdown/ai?lang=en', [
            'dump' => "email Alex\nfind attachment",
            'focus' => 'answer the school email',
            'stuck' => 'avoidance',
            'done' => 'draft sent',
        ])
            ->assertOk()
            ->assertJsonPath('available', false)
            ->assertJsonPath('title', 'answer the school email')
            ->assertJsonPath('done', 'draft sent')
            ->assertJsonPath('now.1', 'Put “answer the school email” in front of you.');
    }

    public function test_goal_builder_ai_falls_back_when_not_configured(): void
    {
        config(['services.openai.key' => null]);

        $this->postJson('/tools/goal-builder/ai?lang=en', [
            'goal' => 'clean my room',
            'why' => 'I want less stress tomorrow',
            'blocker' => 'too many piles',
            'energy' => 'low',
        ])
            ->assertOk()
            ->assertJsonPath('available', false)
            ->assertJsonPath('title', 'clean my room')
            ->assertJsonPath('first', 'Write “clean my room” on a visible note.')
            ->assertJsonPath('support', 'Plan for this blocker: too many piles. Lower the task size before trying harder.');
    }

    public function test_goal_builder_ai_returns_configured_plan(): void
    {
        config([
            'services.openai.key' => 'test-key',
            'services.openai.model' => 'test-model',
        ]);

        Http::fake([
            'https://api.openai.com/v1/responses' => Http::response([
                'output_text' => json_encode([
                    'title' => 'Clear one visible corner',
                    'first' => 'Put one empty bag by the door.',
                    'microsteps' => [
                        'Pick up only floor trash.',
                        'Put laundry in one basket.',
                        'Clear one chair.',
                        'Stop after the timer.',
                    ],
                    'support' => 'Use music and a 5-minute timer.',
                    'done' => 'Done enough is one clearer restart point.',
                ]),
            ]),
        ]);

        $this->postJson('/tools/goal-builder/ai?lang=en', [
            'goal' => 'clean my room',
            'why' => '',
            'blocker' => 'too many piles',
            'energy' => 'medium',
        ])
            ->assertOk()
            ->assertJsonPath('available', true)
            ->assertJsonPath('title', 'Clear one visible corner')
            ->assertJsonPath('first', 'Put one empty bag by the door.')
            ->assertJsonPath('microsteps.0', 'Pick up only floor trash.');

        Http::assertSentCount(1);
    }

    public function test_daily_planner_ai_falls_back_when_not_configured(): void
    {
        config(['services.openai.key' => null]);

        $this->postJson('/tools/daily-planner/ai?lang=en', [
            'must' => "pay rent\nemail Sam",
            'should' => 'fold laundry',
            'body' => '',
            'scary' => 'call the clinic',
            'recovery' => '',
        ])
            ->assertOk()
            ->assertJsonPath('available', false)
            ->assertJsonPath('must.0', 'pay rent')
            ->assertJsonPath('should.0', 'fold laundry')
            ->assertJsonPath('body', 'Drink water and eat something simple before judging the day.')
            ->assertJsonPath('scary', 'Open or touch “call the clinic” for two minutes only.');
    }

    public function test_daily_planner_ai_returns_configured_plan(): void
    {
        config([
            'services.openai.key' => 'test-key',
            'services.openai.model' => 'test-model',
        ]);

        Http::fake([
            'https://api.openai.com/v1/responses' => Http::response([
                'output_text' => json_encode([
                    'must' => [
                        'Pay rent before lunch.',
                        'Email Sam one sentence.',
                    ],
                    'should' => [
                        'Fold one laundry category.',
                    ],
                    'body' => 'Eat breakfast and refill water first.',
                    'scary' => 'Open the clinic number and stop there.',
                    'recovery' => 'Restart with a 5-minute timer after lunch.',
                ]),
            ]),
        ]);

        $this->postJson('/tools/daily-planner/ai?lang=en', [
            'must' => "pay rent\nemail Sam",
            'should' => 'fold laundry',
            'body' => 'breakfast',
            'scary' => 'call the clinic',
            'recovery' => '',
        ])
            ->assertOk()
            ->assertJsonPath('available', true)
            ->assertJsonPath('must.0', 'Pay rent before lunch.')
            ->assertJsonPath('body', 'Eat breakfast and refill water first.')
            ->assertJsonPath('recovery', 'Restart with a 5-minute timer after lunch.');

        Http::assertSentCount(1);
    }

    public function test_time_block_ai_falls_back_when_not_configured(): void
    {
        config(['services.openai.key' => null]);

        $this->postJson('/tools/time-block/ai?lang=en', [
            'start' => '09:00',
            'end' => '12:00',
            'must' => "pay bill\nwrite intro",
            'fixed' => '11:00 call',
            'buffer' => 'medium',
            'energy' => 'low',
            'recovery' => 'lunch away from laptop',
        ])
            ->assertOk()
            ->assertJsonPath('available', false)
            ->assertJsonPath('title', 'Time blocks from 09:00 to 12:00')
            ->assertJsonPath('blocks.0', '09:00 - 09:20: pay bill')
            ->assertJsonPath('fixed.0', '11:00 call')
            ->assertJsonPath('recovery', 'Recovery block: lunch away from laptop. Put it before you crash, not only after.');
    }

    public function test_time_block_ai_returns_configured_plan(): void
    {
        config([
            'services.openai.key' => 'test-key',
            'services.openai.model' => 'test-model',
        ]);

        Http::fake([
            'https://api.openai.com/v1/responses' => Http::response([
                'output_text' => json_encode([
                    'title' => 'Buffered morning map',
                    'blocks' => [
                        '09:00 - 09:25: Pay bill',
                        '09:40 - 10:05: Write intro',
                    ],
                    'fixed' => [
                        '11:00 call, prep at 10:45',
                    ],
                    'buffers' => [
                        'Keep 15 minutes after each block.',
                        'Leave one blank block.',
                    ],
                    'recovery' => 'Lunch away from laptop at 12:00.',
                    'fallback' => 'If the day slips, restart at the next visible block.',
                ]),
            ]),
        ]);

        $this->postJson('/tools/time-block/ai?lang=en', [
            'start' => '09:00',
            'end' => '12:00',
            'must' => "pay bill\nwrite intro",
            'fixed' => '11:00 call',
            'buffer' => 'medium',
            'energy' => 'low',
            'recovery' => 'lunch away from laptop',
        ])
            ->assertOk()
            ->assertJsonPath('available', true)
            ->assertJsonPath('title', 'Buffered morning map')
            ->assertJsonPath('blocks.0', '09:00 - 09:25: Pay bill')
            ->assertJsonPath('fixed.0', '11:00 call, prep at 10:45')
            ->assertJsonPath('fallback', 'If the day slips, restart at the next visible block.');

        Http::assertSentCount(1);
    }

    public function test_body_needs_ai_falls_back_when_not_configured(): void
    {
        config(['services.openai.key' => null]);

        $this->postJson('/tools/body-needs/ai?lang=en', [
            'food' => 'empty',
            'water' => 'low',
            'meds' => 'needed',
            'sleep' => 'low',
            'signal' => 'tense',
            'task' => 'answer email',
        ])
            ->assertOk()
            ->assertJsonPath('available', false)
            ->assertJsonPath('title', 'Body-first reset')
            ->assertJsonPath('first', 'Eat the easiest available food with protein, fat, or slow carbs. Good enough counts.')
            ->assertJsonPath('steps.0', 'Drink water now or place it beside the next task.')
            ->assertJsonPath('task', 'Adjust answer email: make it two minutes, seated/standing as needed, and stop before body signals become louder.');
    }

    public function test_body_needs_ai_returns_configured_plan(): void
    {
        config([
            'services.openai.key' => 'test-key',
            'services.openai.model' => 'test-model',
        ]);

        Http::fake([
            'https://api.openai.com/v1/responses' => Http::response([
                'output_text' => json_encode([
                    'title' => 'Gentle body reset',
                    'first' => 'Drink water and eat one simple snack before the email.',
                    'steps' => [
                        'Put both feet on the floor.',
                        'Check the normal medication reminder once.',
                        'Make the email a two-minute draft.',
                    ],
                    'checks' => [
                        'Food is visible.',
                        'Water is reachable.',
                        'Jaw and shoulders are softer.',
                    ],
                    'task' => 'Answer email becomes opening the thread and writing one sentence.',
                    'stop' => 'Stop after one body need is handled and the draft has a restart point.',
                ]),
            ]),
        ]);

        $this->postJson('/tools/body-needs/ai?lang=en', [
            'food' => 'snack',
            'water' => 'low',
            'meds' => 'needed',
            'sleep' => 'medium',
            'signal' => 'tense',
            'task' => 'answer email',
        ])
            ->assertOk()
            ->assertJsonPath('available', true)
            ->assertJsonPath('title', 'Gentle body reset')
            ->assertJsonPath('first', 'Drink water and eat one simple snack before the email.')
            ->assertJsonPath('steps.2', 'Make the email a two-minute draft.')
            ->assertJsonPath('stop', 'Stop after one body need is handled and the draft has a restart point.');

        Http::assertSentCount(1);
    }

    public function test_medication_refill_ai_falls_back_when_not_configured(): void
    {
        config(['services.openai.key' => null]);

        $this->postJson('/tools/medication-refill/ai?lang=en', [
            'mode' => 'out',
            'medicine' => 'ADHD medication',
            'supply' => 'out today',
            'blocker' => 'ashamed to call late',
            'contact' => 'pharmacy',
            'deadline' => 'before work tomorrow',
        ])
            ->assertOk()
            ->assertJsonPath('available', false)
            ->assertJsonPath('title', 'Medication refill rescue plan')
            ->assertJsonPath('first', 'Set a two-minute timer and find the bottle, pharmacy app, or latest message for ADHD medication. Current supply: out today.')
            ->assertJsonPath('steps.0', 'Contact the pharmacist or prescriber today and say you are out.')
            ->assertJsonPath('steps.3', 'Park the blocker as an ask, not a personal failure: ashamed to call late.')
            ->assertJsonPath('safety', 'This tool is for organizing refill steps, not medical advice. Ask a pharmacist, prescriber, or qualified clinician before changing, skipping, stopping, or restarting medication.');
    }

    public function test_medication_refill_ai_returns_configured_plan(): void
    {
        config([
            'services.openai.key' => 'test-key',
            'services.openai.model' => 'test-model',
        ]);

        Http::fake([
            'https://api.openai.com/v1/responses' => Http::response([
                'output_text' => json_encode([
                    'title' => 'Refill plan for ADHD medication',
                    'first' => 'Open the pharmacy app and find the last refill message.',
                    'steps' => [
                        'Check whether the refill button is available.',
                        'Message the pharmacy with the supply and deadline.',
                        'Write the follow-up time before closing the app.',
                    ],
                    'script' => 'Hi, I am trying to prevent a gap. Could you tell me the next refill step?',
                    'questions' => [
                        'Is the refill available now?',
                        'Does the prescriber need to approve anything?',
                    ],
                    'safety' => 'Ask a pharmacist or prescriber before changing, skipping, stopping, or restarting medication.',
                    'stop' => 'Stop when the refill is requested or the next contact is written down.',
                ]),
            ]),
        ]);

        $this->postJson('/tools/medication-refill/ai?lang=en', [
            'mode' => 'running_low',
            'medicine' => 'ADHD medication',
            'supply' => '3 pills left',
            'blocker' => 'app confusing',
            'contact' => 'pharmacy',
            'deadline' => 'Friday',
        ])
            ->assertOk()
            ->assertJsonPath('available', true)
            ->assertJsonPath('title', 'Refill plan for ADHD medication')
            ->assertJsonPath('first', 'Open the pharmacy app and find the last refill message.')
            ->assertJsonPath('steps.1', 'Message the pharmacy with the supply and deadline.')
            ->assertJsonPath('questions.0', 'Is the refill available now?')
            ->assertJsonPath('stop', 'Stop when the refill is requested or the next contact is written down.');

        Http::assertSentCount(1);
    }

    public function test_focus_sprint_ai_falls_back_when_not_configured(): void
    {
        config(['services.openai.key' => null]);

        $this->postJson('/tools/focus-sprint/ai?lang=en', [
            'task' => 'answer email',
            'mode' => 'avoidance',
            'minutes' => '10',
            'distraction' => 'write it on a sticky note',
            'support' => 'music',
            'reward' => 'make tea',
        ])
            ->assertOk()
            ->assertJsonPath('available', false)
            ->assertJsonPath('title', 'Sprint: answer email')
            ->assertJsonPath('start', 'Set a 10 minutes timer and make contact with answer email for two minutes.')
            ->assertJsonPath('steps.0', 'Name the feeling without arguing with it.')
            ->assertJsonPath('support', 'Support for this sprint: Music or background sound.');
    }

    public function test_focus_sprint_ai_returns_configured_plan(): void
    {
        config([
            'services.openai.key' => 'test-key',
            'services.openai.model' => 'test-model',
        ]);

        Http::fake([
            'https://api.openai.com/v1/responses' => Http::response([
                'output_text' => json_encode([
                    'title' => 'Sprint: email Alex',
                    'start' => 'Open the email thread and set a 10-minute timer.',
                    'steps' => [
                        'Read only the last message.',
                        'Write one plain sentence.',
                        'Stop when the timer rings.',
                    ],
                    'distraction' => 'Park distractions on a sticky note, then return.',
                    'support' => 'Use music quietly in the background.',
                    'reward' => 'Make tea after the timer.',
                    'stop' => 'Stop at one sent draft or one saved restart point.',
                ]),
            ]),
        ]);

        $this->postJson('/tools/focus-sprint/ai?lang=en', [
            'task' => 'email Alex',
            'mode' => 'avoidance',
            'minutes' => '10',
            'distraction' => 'sticky note',
            'support' => 'music',
            'reward' => 'tea',
        ])
            ->assertOk()
            ->assertJsonPath('available', true)
            ->assertJsonPath('title', 'Sprint: email Alex')
            ->assertJsonPath('start', 'Open the email thread and set a 10-minute timer.')
            ->assertJsonPath('steps.1', 'Write one plain sentence.')
            ->assertJsonPath('reward', 'Make tea after the timer.');

        Http::assertSentCount(1);
    }

    public function test_emotional_reset_ai_falls_back_when_not_configured(): void
    {
        config(['services.openai.key' => null]);

        $this->postJson('/tools/emotional-reset/ai?lang=en', [
            'trigger' => 'missed deadline',
            'intensity' => 'high',
            'body' => 'tight',
            'story' => 'I am overwhelmed, not broken',
            'next' => 'send one update',
            'support' => 'Sam',
        ])
            ->assertOk()
            ->assertJsonPath('available', false)
            ->assertJsonPath('title', 'Reset for: missed deadline')
            ->assertJsonPath('first', 'Loosen one muscle group and exhale longer than you inhale.')
            ->assertJsonPath('ground.0', 'Pause decisions, messages, spending, and self-criticism for ten minutes.')
            ->assertJsonPath('next', 'Next tiny action: send one update. Keep it small enough to do while imperfect.');
    }

    public function test_emotional_reset_ai_returns_configured_plan(): void
    {
        config([
            'services.openai.key' => 'test-key',
            'services.openai.model' => 'test-model',
        ]);

        Http::fake([
            'https://api.openai.com/v1/responses' => Http::response([
                'output_text' => json_encode([
                    'title' => 'Reset for the late reply',
                    'first' => 'Put both feet down and unclench your jaw.',
                    'ground' => [
                        'Name the feeling once.',
                        'Drink water before replying.',
                        'Write one line only.',
                    ],
                    'reframe' => 'A late reply is a repairable moment, not proof you are bad.',
                    'next' => 'Send one plain update.',
                    'script' => 'Hi Sam, I am spiraling and need company for one tiny reset.',
                    'stop' => 'Stop after the update is drafted or your body is calmer.',
                ]),
            ]),
        ]);

        $this->postJson('/tools/emotional-reset/ai?lang=en', [
            'trigger' => 'late reply',
            'intensity' => 'medium',
            'body' => 'tight',
            'story' => '',
            'next' => 'send an update',
            'support' => 'Sam',
        ])
            ->assertOk()
            ->assertJsonPath('available', true)
            ->assertJsonPath('title', 'Reset for the late reply')
            ->assertJsonPath('first', 'Put both feet down and unclench your jaw.')
            ->assertJsonPath('ground.2', 'Write one line only.')
            ->assertJsonPath('stop', 'Stop after the update is drafted or your body is calmer.');

        Http::assertSentCount(1);
    }

    public function test_energy_crash_ai_falls_back_when_not_configured(): void
    {
        config(['services.openai.key' => null]);

        $this->postJson('/tools/energy-crash/ai?lang=en', [
            'state' => 'empty',
            'must' => 'pay rent',
            'body' => 'food',
            'drop' => 'laundry',
            'support' => 'Sam',
            'time' => '10',
        ])
            ->assertOk()
            ->assertJsonPath('available', false)
            ->assertJsonPath('title', 'Energy plan: Empty / no fuel')
            ->assertJsonPath('first', 'For the next 10 minutes, only start: pay rent. Do not solve the whole day.')
            ->assertJsonPath('body', 'Eat something easy or put food where you can reach it.')
            ->assertJsonPath('steps.0', 'Lower the target to the smallest protective version.');
    }

    public function test_energy_crash_ai_returns_configured_plan(): void
    {
        config([
            'services.openai.key' => 'test-key',
            'services.openai.model' => 'test-model',
        ]);

        Http::fake([
            'https://api.openai.com/v1/responses' => Http::response([
                'output_text' => json_encode([
                    'title' => 'Low-capacity rent plan',
                    'first' => 'Open the rent portal for five minutes only.',
                    'body' => 'Eat toast before touching the portal.',
                    'steps' => [
                        'Put the laptop on the table.',
                        'Open only the rent tab.',
                        'Stop after the amount is visible.',
                    ],
                    'drop' => 'Laundry waits until tomorrow.',
                    'script' => 'Hi Sam, can you check in after five minutes?',
                    'stop' => 'Stop when rent is paid or the restart point is saved.',
                ]),
            ]),
        ]);

        $this->postJson('/tools/energy-crash/ai?lang=en', [
            'state' => 'empty',
            'must' => 'pay rent',
            'body' => 'food',
            'drop' => 'laundry',
            'support' => 'Sam',
            'time' => '5',
        ])
            ->assertOk()
            ->assertJsonPath('available', true)
            ->assertJsonPath('title', 'Low-capacity rent plan')
            ->assertJsonPath('first', 'Open the rent portal for five minutes only.')
            ->assertJsonPath('steps.2', 'Stop after the amount is visible.')
            ->assertJsonPath('drop', 'Laundry waits until tomorrow.');

        Http::assertSentCount(1);
    }

    public function test_decision_priority_ai_falls_back_when_not_configured(): void
    {
        config(['services.openai.key' => null]);

        $this->postJson('/tools/decision-priority/ai?lang=en', [
            'options' => "pay bill\nreply to email\nshower",
            'urgency' => 'today',
            'energy' => 'low',
            'consequence' => 'money',
            'relief' => '',
            'support' => 'Sam',
        ])
            ->assertOk()
            ->assertJsonPath('available', false)
            ->assertJsonPath('title', 'Start here: pay bill')
            ->assertJsonPath('chosen', 'pay bill')
            ->assertJsonPath('parked.0', 'reply to email')
            ->assertJsonPath('why', 'This is first because it protects money, housing, bills, benefits, or admin safety.');
    }

    public function test_decision_priority_ai_returns_configured_plan(): void
    {
        config([
            'services.openai.key' => 'test-key',
            'services.openai.model' => 'test-model',
        ]);

        Http::fake([
            'https://api.openai.com/v1/responses' => Http::response([
                'output_text' => json_encode([
                    'title' => 'Start here: pay rent',
                    'chosen' => 'pay rent',
                    'why' => 'This protects housing and stops the loudest consequence.',
                    'first' => 'Open the rent portal and look only for the amount.',
                    'steps' => [
                        'Open the portal.',
                        'Find the amount due.',
                        'Stop or pay after two minutes.',
                    ],
                    'parked' => [
                        'Reply to email',
                        'Shower',
                    ],
                    'script' => 'Hi Sam, I am starting with rent. Can you check in after ten minutes?',
                    'stop' => 'Stop choosing once rent is started or the restart point is saved.',
                ]),
            ]),
        ]);

        $this->postJson('/tools/decision-priority/ai?lang=en', [
            'options' => "pay rent\nreply to email\nshower",
            'urgency' => 'today',
            'energy' => 'low',
            'consequence' => 'money',
            'relief' => '',
            'support' => 'Sam',
        ])
            ->assertOk()
            ->assertJsonPath('available', true)
            ->assertJsonPath('title', 'Start here: pay rent')
            ->assertJsonPath('chosen', 'pay rent')
            ->assertJsonPath('steps.1', 'Find the amount due.')
            ->assertJsonPath('parked.0', 'Reply to email')
            ->assertJsonPath('stop', 'Stop choosing once rent is started or the restart point is saved.');

        Http::assertSentCount(1);
    }

    public function test_money_admin_ai_falls_back_when_not_configured(): void
    {
        config(['services.openai.key' => null]);

        $this->postJson('/tools/money-admin/ai?lang=en', [
            'task' => 'pay electricity bill',
            'category' => 'bill',
            'urgency' => 'overdue',
            'blocker' => 'afraid of late fee',
            'contact' => 'billing office',
            'outcome' => 'know whether a payment plan exists',
        ])
            ->assertOk()
            ->assertJsonPath('available', false)
            ->assertJsonPath('title', 'Rescue: pay electricity bill')
            ->assertJsonPath('steps.0', 'Find the bill, account page, or reminder.')
            ->assertJsonPath('script', 'Hello billing office, I am trying to sort out pay electricity bill. Can you tell me the current amount due, the deadline, and whether a payment plan or lower immediate payment is possible?')
            ->assertJsonPath('questions.0', 'Is there a late fee, grace period, payment plan, or hardship option?')
            ->assertJsonPath('stop', 'Stop when know whether a payment plan exists or when you have created one clear next step. This is a rescue, not a punishment session.');
    }

    public function test_money_admin_ai_returns_configured_plan(): void
    {
        config([
            'services.openai.key' => 'test-key',
            'services.openai.model' => 'test-model',
        ]);

        Http::fake([
            'https://api.openai.com/v1/responses' => Http::response([
                'output_text' => json_encode([
                    'title' => 'Rescue: rent payment plan',
                    'first' => 'Open the rent portal and find the current amount only.',
                    'steps' => [
                        'Write the amount and due date.',
                        'Find one contact path for the landlord or billing office.',
                        'Send the payment-plan question before comparing every option.',
                    ],
                    'script' => 'Hello, I am trying to catch up on rent. Can you tell me whether a payment plan is possible?',
                    'questions' => [
                        'What amount is due today?',
                        'Is there a payment plan or grace period?',
                    ],
                    'stop' => 'Stop when the message is sent or the next contact time is saved.',
                ]),
            ]),
        ]);

        $this->postJson('/tools/money-admin/ai?lang=en', [
            'task' => 'rent payment plan',
            'category' => 'bill',
            'urgency' => 'today',
            'blocker' => 'scared to ask',
            'contact' => 'landlord',
            'outcome' => 'send one question',
        ])
            ->assertOk()
            ->assertJsonPath('available', true)
            ->assertJsonPath('title', 'Rescue: rent payment plan')
            ->assertJsonPath('first', 'Open the rent portal and find the current amount only.')
            ->assertJsonPath('steps.1', 'Find one contact path for the landlord or billing office.')
            ->assertJsonPath('questions.0', 'What amount is due today?')
            ->assertJsonPath('stop', 'Stop when the message is sent or the next contact time is saved.');

        Http::assertSentCount(1);
    }

    public function test_task_breakdown_ai_returns_configured_plan(): void
    {
        config([
            'services.openai.key' => 'test-key',
            'services.openai.model' => 'test-model',
        ]);

        Http::fake([
            'https://api.openai.com/v1/responses' => Http::response([
                'output_text' => json_encode([
                    'title' => 'Answer Alex about the school form',
                    'now' => [
                        'Open the email thread.',
                        'Set a five-minute timer.',
                    ],
                    'microsteps' => [
                        'Open the attachment.',
                        'Find the deadline.',
                        'Write one plain sentence.',
                        'Add the missing question.',
                    ],
                    'hidden' => [
                        'Find the form link.',
                        'Decide what good enough means.',
                    ],
                    'done' => 'Done enough is a sent draft or a saved question.',
                    'reset' => 'If stuck, reopen the thread and read only the last message.',
                ]),
            ]),
        ]);

        $this->postJson('/tools/task-breakdown/ai?lang=en', [
            'dump' => "school form\nemail Alex",
            'focus' => 'answer the school email',
            'stuck' => 'unclear',
            'done' => '',
        ])
            ->assertOk()
            ->assertJsonPath('available', true)
            ->assertJsonPath('title', 'Answer Alex about the school form')
            ->assertJsonPath('microsteps.0', 'Open the attachment.')
            ->assertJsonPath('reset', 'If stuck, reopen the thread and read only the last message.');

        Http::assertSentCount(1);
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
