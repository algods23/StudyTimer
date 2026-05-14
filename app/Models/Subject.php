<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    /** @use HasFactory<\Database\Factories\SubjectFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'color',
        'weekly_goal_minutes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function studySessions()
    {
        return $this->hasMany(StudySession::class);
    }

    public function getTotalMinutesAttribute(): int
    {
        return (int) $this->studySessions()->sum('duration_minutes');
    }
}
