<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Exercise;
use App\Models\UserProgress;
use Illuminate\Http\Request;

class ExerciseController extends Controller
{
    public function show($id)
    {
        $exercise = Exercise::with(['lesson.course'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'exercise' => $exercise
        ]);
    }

    public function submitAnswer(Request $request, $id)
    {
        $request->validate([
            'answer' => 'required|string'
        ]);

        $user = auth()->user();
        $exercise = Exercise::with('lesson')->findOrFail($id);
        
        $isCorrect = $exercise->checkAnswer($request->answer);

        // Get or create progress
        $progress = UserProgress::firstOrCreate(
            [
                'user_id' => $user->id,
                'exercise_id' => $id
            ],
            [
                'course_id' => $exercise->lesson->course_id,
                'lesson_id' => $exercise->lesson_id,
                'status' => 'in_progress',
                'started_at' => now()
            ]
        );

        // Update progress
        $progress->attempts++;
        $progress->total_questions++;
        
        if ($isCorrect) {
            $progress->correct_answers++;
            $progress->xp_earned += $exercise->xp_reward;
            $progress->status = 'completed';
            $progress->completed_at = now();
            
            // Update user XP
            $user->addXP($exercise->xp_reward);
        }

        $progress->updateAccuracy();

        return response()->json([
            'success' => true,
            'is_correct' => $isCorrect,
            'correct_answer' => $isCorrect ? null : $exercise->correct_answer,
            'explanation' => $exercise->explanation,
            'xp_earned' => $isCorrect ? $exercise->xp_reward : 0,
            'progress' => $progress
        ]);
    }

    public function getLessonExercises($lessonId)
    {
        $exercises = Exercise::where('lesson_id', $lessonId)
            ->orderBy('order')
            ->get();

        $user = auth()->user();
        $progress = [];

        if ($user) {
            $progress = UserProgress::where('user_id', $user->id)
                ->whereIn('exercise_id', $exercises->pluck('id'))
                ->get()
                ->keyBy('exercise_id');
        }

        return response()->json([
            'success' => true,
            'exercises' => $exercises,
            'progress' => $progress
        ]);
    }
}
