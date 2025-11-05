<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::where('is_active', true)
            ->orderBy('order')
            ->with(['lessons' => function ($query) {
                $query->orderBy('order');
            }])
            ->get();

        return response()->json([
            'success' => true,
            'courses' => $courses
        ]);
    }

    public function show($id)
    {
        $course = Course::with(['lessons.exercises'])
            ->findOrFail($id);

        $user = auth()->user();
        $progress = null;

        if ($user) {
            $progress = $course->userProgress()
                ->where('user_id', $user->id)
                ->get();
        }

        return response()->json([
            'success' => true,
            'course' => $course,
            'progress' => $progress
        ]);
    }

    public function getUserCourseProgress($courseId)
    {
        $user = auth()->user();
        $course = Course::findOrFail($courseId);

        $progress = $course->userProgress()
            ->where('user_id', $user->id)
            ->with(['lesson', 'exercise'])
            ->get();

        $completedLessons = $progress->where('status', 'completed')
            ->where('lesson_id', '!=', null)
            ->unique('lesson_id')
            ->count();

        $totalLessons = $course->lessons()->count();
        $completionPercentage = $totalLessons > 0 
            ? ($completedLessons / $totalLessons) * 100 
            : 0;

        return response()->json([
            'success' => true,
            'progress' => $progress,
            'stats' => [
                'completed_lessons' => $completedLessons,
                'total_lessons' => $totalLessons,
                'completion_percentage' => round($completionPercentage, 2)
            ]
        ]);
    }
}
