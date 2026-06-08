<?php

use App\Http\Controllers\AssessmentController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AssessmentController::class, 'show'])->name('assessment.show');
Route::post('/assessment/score', [AssessmentController::class, 'score'])->name('assessment.score');
Route::get('/dashboard', [AssessmentController::class, 'dashboard'])->name('dashboard');
Route::get('/resources', [AssessmentController::class, 'resources'])->name('resources');
Route::get('/tools/goal-builder', [AssessmentController::class, 'goalBuilder'])->name('tools.goal');
Route::get('/tools/daily-planner', [AssessmentController::class, 'dailyPlanner'])->name('tools.planner');
Route::get('/tools/time-block', [AssessmentController::class, 'timeBlock'])->name('tools.time');
Route::get('/tools/body-needs', [AssessmentController::class, 'bodyNeeds'])->name('tools.body');
Route::get('/tools/routine-builder', [AssessmentController::class, 'routineBuilder'])->name('tools.routine');
Route::get('/tools/weekly-reset', [AssessmentController::class, 'weeklyReset'])->name('tools.weekly');
Route::get('/tools/communication-repair', [AssessmentController::class, 'communicationRepair'])->name('tools.communication');
Route::get('/tools/energy-crash', [AssessmentController::class, 'energyCrash'])->name('tools.energy');
Route::get('/tools/decision-priority', [AssessmentController::class, 'decisionPriority'])->name('tools.decision');
Route::get('/tools/focus-sprint', [AssessmentController::class, 'focusSprint'])->name('tools.focus');
Route::get('/tools/emotional-reset', [AssessmentController::class, 'emotionalReset'])->name('tools.emotion');
Route::get('/tools/transition-rescue', [AssessmentController::class, 'transitionRescue'])->name('tools.transition');
Route::get('/tools/appointment-prep', [AssessmentController::class, 'appointmentPrep'])->name('tools.appointment');
Route::get('/tools/care-notes', [AssessmentController::class, 'careNotes'])->name('tools.care');
Route::get('/tools/support-request', [AssessmentController::class, 'supportRequest'])->name('tools.support');
Route::get('/tools/home-reset', [AssessmentController::class, 'homeReset'])->name('tools.home');
Route::get('/tools/money-admin', [AssessmentController::class, 'moneyAdmin'])->name('tools.money');
Route::get('/tools/provider-shortlist', [AssessmentController::class, 'providerShortlist'])->name('tools.providers');
Route::get('/tools/access-plan', [AssessmentController::class, 'accessPlan'])->name('tools.access');
Route::get('/tools/task-breakdown', [AssessmentController::class, 'taskBreakdown'])->name('tools.task');
Route::get('/tools/reminders', [AssessmentController::class, 'reminders'])->name('tools.reminders');
Route::get('/tools/symptom-tracker', [AssessmentController::class, 'symptomTracker'])->name('tools.tracker');
