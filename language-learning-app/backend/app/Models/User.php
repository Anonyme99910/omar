<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'total_xp',
        'current_streak',
        'longest_streak',
        'last_practice_date',
        'role',
        'is_guest',
        'guest_token'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_practice_date' => 'date',
        'is_guest' => 'boolean',
        'total_xp' => 'integer',
        'current_streak' => 'integer',
        'longest_streak' => 'integer'
    ];

    public function progress()
    {
        return $this->hasMany(UserProgress::class);
    }

    public function achievements()
    {
        return $this->belongsToMany(Achievement::class, 'user_achievements')
            ->withTimestamps()
            ->withPivot('earned_at');
    }

    public function updateStreak()
    {
        $today = now()->toDateString();
        $lastPractice = $this->last_practice_date?->toDateString();

        if ($lastPractice === $today) {
            return; // Already practiced today
        }

        $yesterday = now()->subDay()->toDateString();

        if ($lastPractice === $yesterday) {
            // Continue streak
            $this->current_streak++;
        } else {
            // Reset streak
            $this->current_streak = 1;
        }

        if ($this->current_streak > $this->longest_streak) {
            $this->longest_streak = $this->current_streak;
        }

        $this->last_practice_date = $today;
        $this->save();
    }

    public function addXP($amount)
    {
        $this->total_xp += $amount;
        $this->save();
    }
}
