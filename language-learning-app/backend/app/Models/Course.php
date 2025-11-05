<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'language_code',
        'flag_icon',
        'description',
        'difficulty',
        'color_primary',
        'color_secondary',
        'total_xp',
        'is_active',
        'order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'total_xp' => 'integer',
        'order' => 'integer'
    ];

    public function lessons()
    {
        return $this->hasMany(Lesson::class)->orderBy('order');
    }

    public function userProgress()
    {
        return $this->hasMany(UserProgress::class);
    }

    public function getUserProgress($userId)
    {
        return $this->userProgress()->where('user_id', $userId)->first();
    }
}
