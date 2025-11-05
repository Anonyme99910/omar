<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\UserProgress;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function show($id)
    {
        $lesson = Lesson::with(['exercises', 'course'])
            ->findOrFail($id);

        $user = auth()->user();
        $progress = null;

        if ($user) {
            $progress = UserProgress::where('user_id', $user->id)
                ->where('lesson_id', $id)
                ->first();
        }

        return response()->json([
            'success' => true,
            'lesson' => $lesson,
            'progress' => $progress
        ]);
    }

    public function start($id)
    {
        $user = auth()->user();
        $lesson = Lesson::findOrFail($id);

        $progress = UserProgress::updateOrCreate(
            [
                'user_id' => $user->id,
                'lesson_id' => $id,
                'exercise_id' => null
            ],
            [
                'course_id' => $lesson->course_id,
                'status' => 'in_progress',
                'started_at' => now()
            ]
        );

        return response()->json([
            'success' => true,
            'progress' => $progress,
            'message' => 'Lesson started'
        ]);
    }

    public function complete($id)
    {
        $user = auth()->user();
        $lesson = Lesson::with('exercises')->findOrFail($id);

        $progress = UserProgress::where('user_id', $user->id)
            ->where('lesson_id', $id)
            ->where('exercise_id', null)
            ->first();

        if (!$progress) {
            return response()->json([
                'success' => false,
                'message' => 'Lesson not started'
            ], 400);
        }

        $progress->update([
            'status' => 'completed',
            'completed_at' => now(),
            'xp_earned' => $lesson->xp_reward
        ]);

        // Update user XP and streak
        $user->addXP($lesson->xp_reward);
        $user->updateStreak();

        return response()->json([
            'success' => true,
            'progress' => $progress,
            'xp_earned' => $lesson->xp_reward,
            'message' => 'Lesson completed!'
        ]);
    }
}
