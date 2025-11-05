<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProgress extends Model
{
    use HasFactory;

    protected $table = 'user_progress';

    protected $fillable = [
        'user_id',
        'course_id',
        'lesson_id',
        'exercise_id',
        'status',
        'xp_earned',
        'attempts',
        'correct_answers',
        'total_questions',
        'accuracy',
        'started_at',
        'completed_at'
    ];

    protected $casts = [
        'xp_earned' => 'integer',
        'attempts' => 'integer',
        'correct_answers' => 'integer',
        'total_questions' => 'integer',
        'accuracy' => 'decimal:2',
        'started_at' => 'datetime',
        'completed_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function exercise()
    {
        return $this->belongsTo(Exercise::class);
    }

    public function updateAccuracy()
    {
        if ($this->total_questions > 0) {
            $this->accuracy = ($this->correct_answers / $this->total_questions) * 100;
            $this->save();
        }
    }
}
