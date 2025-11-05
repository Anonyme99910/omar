<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exercise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminExerciseController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'lesson_id' => 'required|exists:lessons,id',
            'type' => 'required|in:multiple_choice,translate,speak,listen,match_pairs,fill_blank,word_order',
            'question' => 'required|string',
            'options' => 'nullable|array',
            'correct_answer' => 'required|string',
            'explanation' => 'nullable|string',
            'xp_reward' => 'integer|min:1',
            'order' => 'integer'
        ]);

        $exercise = Exercise::create($validated);

        return response()->json([
            'success' => true,
            'exercise' => $exercise,
            'message' => 'Exercise created successfully'
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $exercise = Exercise::findOrFail($id);

        $validated = $request->validate([
            'type' => 'in:multiple_choice,translate,speak,listen,match_pairs,fill_blank,word_order',
            'question' => 'string',
            'options' => 'nullable|array',
            'correct_answer' => 'string',
            'explanation' => 'nullable|string',
            'xp_reward' => 'integer|min:1',
            'order' => 'integer'
        ]);

        $exercise->update($validated);

        return response()->json([
            'success' => true,
            'exercise' => $exercise,
            'message' => 'Exercise updated successfully'
        ]);
    }

    public function destroy($id)
    {
        $exercise = Exercise::findOrFail($id);
        
        // Delete audio files if they exist
        if ($exercise->question_audio) {
            Storage::delete($exercise->question_audio);
        }
        if ($exercise->correct_audio) {
            Storage::delete($exercise->correct_audio);
        }
        
        $exercise->delete();

        return response()->json([
            'success' => true,
            'message' => 'Exercise deleted successfully'
        ]);
    }

    public function uploadAudio(Request $request, $id)
    {
        $request->validate([
            'audio' => 'required|file|mimes:mp3,wav,ogg|max:5120', // 5MB max
            'type' => 'required|in:question,answer'
        ]);

        $exercise = Exercise::findOrFail($id);

        $path = $request->file('audio')->store('audio/exercises', 'public');

        if ($request->type === 'question') {
            // Delete old file if exists
            if ($exercise->question_audio) {
                Storage::delete($exercise->question_audio);
            }
            $exercise->question_audio = $path;
        } else {
            // Delete old file if exists
            if ($exercise->correct_audio) {
                Storage::delete($exercise->correct_audio);
            }
            $exercise->correct_audio = $path;
        }

        $exercise->save();

        return response()->json([
            'success' => true,
            'audio_path' => Storage::url($path),
            'message' => 'Audio uploaded successfully'
        ]);
    }
}
