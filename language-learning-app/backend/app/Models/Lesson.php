<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lesson extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'difficulty',
        'xp_reward',
        'order',
        'icon',
        'is_locked',
        'unlock_after_lesson_id'
    ];

    protected $casts = [
        'is_locked' => 'boolean',
        'xp_reward' => 'integer',
        'order' => 'integer'
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function exercises()
    {
        return $this->hasMany(Exercise::class)->orderBy('order');
    }

    public function userProgress()
    {
        return $this->hasMany(UserProgress::class);
    }

    public function prerequisiteLesson()
    {
        return $this->belongsTo(Lesson::class, 'unlock_after_lesson_id');
    }
}
