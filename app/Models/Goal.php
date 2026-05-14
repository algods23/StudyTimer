<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    /** @use HasFactory<\Database\Factories\GoalFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'daily_goal_minutes',
        'weekly_goal_minutes',
        'monthly_goal_minutes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
