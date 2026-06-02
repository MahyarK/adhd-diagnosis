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
            ->assertJsonPath('counts.hyperactive', 5);
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
}
