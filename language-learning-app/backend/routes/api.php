<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CourseController;
use App\Http\Controllers\API\LessonController;
use App\Http\Controllers\API\ExerciseController;
use App\Http\Controllers\API\Admin\AdminCourseController;
use App\Http\Controllers\API\Admin\AdminLessonController;
use App\Http\Controllers\API\Admin\AdminExerciseController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/guest-login', [AuthController::class, 'guestLogin']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/convert-guest', [AuthController::class, 'convertGuestToUser']);

    // Courses
    Route::get('/courses', [CourseController::class, 'index']);
    Route::get('/courses/{id}', [CourseController::class, 'show']);
    Route::get('/courses/{id}/progress', [CourseController::class, 'getUserCourseProgress']);

    // Lessons
    Route::get('/lessons/{id}', [LessonController::class, 'show']);
    Route::post('/lessons/{id}/start', [LessonController::class, 'start']);
    Route::post('/lessons/{id}/complete', [LessonController::class, 'complete']);

    // Exercises
    Route::get('/exercises/{id}', [ExerciseController::class, 'show']);
    Route::post('/exercises/{id}/submit', [ExerciseController::class, 'submitAnswer']);
    Route::get('/lessons/{lessonId}/exercises', [ExerciseController::class, 'getLessonExercises']);

    // Admin routes
    Route::middleware('admin')->prefix('admin')->group(function () {
        // Courses
        Route::get('/courses', [AdminCourseController::class, 'index']);
        Route::post('/courses', [AdminCourseController::class, 'store']);
        Route::get('/courses/{id}', [AdminCourseController::class, 'show']);
        Route::put('/courses/{id}', [AdminCourseController::class, 'update']);
        Route::delete('/courses/{id}', [AdminCourseController::class, 'destroy']);

        // Lessons
        Route::post('/lessons', [AdminLessonController::class, 'store']);
        Route::put('/lessons/{id}', [AdminLessonController::class, 'update']);
        Route::delete('/lessons/{id}', [AdminLessonController::class, 'destroy']);

        // Exercises
        Route::post('/exercises', [AdminExerciseController::class, 'store']);
        Route::put('/exercises/{id}', [AdminExerciseController::class, 'update']);
        Route::delete('/exercises/{id}', [AdminExerciseController::class, 'destroy']);
        Route::post('/exercises/{id}/upload-audio', [AdminExerciseController::class, 'uploadAudio']);
    });
});
