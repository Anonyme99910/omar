<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exercise extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'lesson_id',
        'type',
        'question',
        'question_audio',
        'options',
        'correct_answer',
        'correct_audio',
        'explanation',
        'xp_reward',
        'order',
        'image'
    ];

    protected $casts = [
        'options' => 'array',
        'xp_reward' => 'integer',
        'order' => 'integer'
    ];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function userProgress()
    {
        return $this->hasMany(UserProgress::class);
    }

    public function checkAnswer($userAnswer)
    {
        return trim(strtolower($userAnswer)) === trim(strtolower($this->correct_answer));
    }
}
