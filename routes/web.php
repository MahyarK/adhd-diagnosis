<?php

use App\Http\Controllers\AssessmentController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AssessmentController::class, 'show'])->name('assessment.show');
Route::post('/assessment/score', [AssessmentController::class, 'score'])->name('assessment.score');
