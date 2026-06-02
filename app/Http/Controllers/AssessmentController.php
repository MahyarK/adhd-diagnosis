<?php

namespace App\Http\Controllers;

use App\Support\AdhdAssessment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssessmentController extends Controller
{
    public function show(): View
    {
        return view('assessment', [
            'sections' => AdhdAssessment::sections(),
        ]);
    }

    public function score(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'answers' => ['required', 'array'],
        ]);

        return response()->json(AdhdAssessment::score($validated['answers']));
    }
}
