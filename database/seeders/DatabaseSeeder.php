<?php

namespace Database\Seeders;

use App\Models\Goal;
use App\Models\Notification;
use App\Models\StudySession;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Study Student',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $subjects = collect([
            ['name' => 'Math', 'color' => '#fb7185', 'weekly_goal_minutes' => 720],
            ['name' => 'Science', 'color' => '#38bdf8', 'weekly_goal_minutes' => 480],
            ['name' => 'English', 'color' => '#a78bfa', 'weekly_goal_minutes' => 360],
            ['name' => 'History', 'color' => '#f59e0b', 'weekly_goal_minutes' => 300],
        ])->map(fn ($subject) => Subject::create($subject + ['user_id' => $user->id]));

        Goal::create([
            'user_id' => $user->id,
            'daily_goal_minutes' => 120,
            'weekly_goal_minutes' => 720,
            'monthly_goal_minutes' => 3000,
        ]);

        foreach (range(0, 13) as $daysAgo) {
            $subject = $subjects->random();
            $minuteOptions = [0, 15, 30, 45];
            $startedAt = now()->subDays($daysAgo)->setTime(rand(8, 20), $minuteOptions[array_rand($minuteOptions)]);
            $duration = rand(25, 95);

            StudySession::create([
                'user_id' => $user->id,
                'subject_id' => $subject->id,
                'started_at' => $startedAt,
                'ended_at' => $startedAt->copy()->addMinutes($duration),
                'duration_minutes' => $duration,
                'mode' => $daysAgo % 2 === 0 ? 'pomodoro' : 'custom',
                'notes' => 'Demo study notes for '.$subject->name.'.',
            ]);
        }

        Notification::create([
            'user_id' => $user->id,
            'title' => 'Evening study reminder',
            'message' => 'Your next focus block is planned for tonight.',
            'type' => 'reminder',
            'scheduled_at' => now()->setTime(19, 0),
        ]);

        Notification::create([
            'user_id' => $user->id,
            'title' => 'Daily goal check',
            'message' => 'Keep going until your daily progress bar reaches 100%.',
            'type' => 'goal',
        ]);
    }
}
