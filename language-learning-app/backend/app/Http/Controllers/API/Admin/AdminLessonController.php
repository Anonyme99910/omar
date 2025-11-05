<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use Illuminate\Http\Request;

class AdminLessonController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'difficulty' => 'required|in:beginner,intermediate,advanced',
            'xp_reward' => 'integer|min:1',
            'order' => 'integer',
            'icon' => 'nullable|string',
            'is_locked' => 'boolean',
            'unlock_after_lesson_id' => 'nullable|exists:lessons,id'
        ]);

        $lesson = Lesson::create($validated);

        return response()->json([
            'success' => true,
            'lesson' => $lesson,
            'message' => 'Lesson created successfully'
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $lesson = Lesson::findOrFail($id);

        $validated = $request->validate([
            'title' => 'string|max:255',
            'description' => 'nullable|string',
            'difficulty' => 'in:beginner,intermediate,advanced',
            'xp_reward' => 'integer|min:1',
            'order' => 'integer',
            'icon' => 'nullable|string',
            'is_locked' => 'boolean',
            'unlock_after_lesson_id' => 'nullable|exists:lessons,id'
        ]);

        $lesson->update($validated);

        return response()->json([
            'success' => true,
            'lesson' => $lesson,
            'message' => 'Lesson updated successfully'
        ]);
    }

    public function destroy($id)
    {
        $lesson = Lesson::findOrFail($id);
        $lesson->delete();

        return response()->json([
            'success' => true,
            'message' => 'Lesson deleted successfully'
        ]);
    }
}
