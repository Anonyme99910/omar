<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminCourseController extends Controller
{
    public function index()
    {
        $courses = Course::withCount('lessons')
            ->orderBy('order')
            ->get();

        return response()->json([
            'success' => true,
            'courses' => $courses
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'language_code' => 'required|string|max:10',
            'flag_icon' => 'nullable|string',
            'description' => 'nullable|string',
            'difficulty' => 'required|in:beginner,intermediate,advanced',
            'color_primary' => 'nullable|string|max:7',
            'color_secondary' => 'nullable|string|max:7',
            'is_active' => 'boolean',
            'order' => 'integer'
        ]);

        $course = Course::create($validated);

        return response()->json([
            'success' => true,
            'course' => $course,
            'message' => 'Course created successfully'
        ], 201);
    }

    public function show($id)
    {
        $course = Course::with(['lessons.exercises'])
            ->withCount('lessons')
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'course' => $course
        ]);
    }

    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        $validated = $request->validate([
            'name' => 'string|max:255',
            'language_code' => 'string|max:10',
            'flag_icon' => 'nullable|string',
            'description' => 'nullable|string',
            'difficulty' => 'in:beginner,intermediate,advanced',
            'color_primary' => 'nullable|string|max:7',
            'color_secondary' => 'nullable|string|max:7',
            'is_active' => 'boolean',
            'order' => 'integer'
        ]);

        $course->update($validated);

        return response()->json([
            'success' => true,
            'course' => $course,
            'message' => 'Course updated successfully'
        ]);
    }

    public function destroy($id)
    {
        $course = Course::findOrFail($id);
        $course->delete();

        return response()->json([
            'success' => true,
            'message' => 'Course deleted successfully'
        ]);
    }
}
