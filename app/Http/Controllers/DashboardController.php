<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();
        $goal = $user->goal()->firstOrCreate([]);
        $now = now();

        // Dashboard totals are calculated from saved sessions so charts always match history.
        $todayMinutes = $user->studySessions()->whereDate('started_at', $now)->sum('duration_minutes');
        $weekMinutes = $user->studySessions()->whereBetween('started_at', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()])->sum('duration_minutes');
        $monthMinutes = $user->studySessions()->whereBetween('started_at', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()])->sum('duration_minutes');

        $subjects = $user->subjects()
            ->withSum('studySessions as total_minutes', 'duration_minutes')
            ->orderBy('name')
            ->get();

        $dailyTrend = collect(range(6, 0))->map(function ($daysAgo) use ($user) {
            $date = now()->subDays($daysAgo);

            return [
                'label' => $date->format('D'),
                'minutes' => (int) $user->studySessions()->whereDate('started_at', $date)->sum('duration_minutes'),
            ];
        });

        return view('dashboard', [
            'goal' => $goal,
            'todayMinutes' => (int) $todayMinutes,
            'weekMinutes' => (int) $weekMinutes,
            'monthMinutes' => (int) $monthMinutes,
            'goalPercent' => $goal->daily_goal_minutes ? min(100, round($todayMinutes / $goal->daily_goal_minutes * 100)) : 0,
            'subjects' => $subjects,
            'dailyTrend' => $dailyTrend,
            'notifications' => $user->studyNotifications()->latest()->limit(5)->get(),
            'upcomingSessions' => [
                ['title' => 'Deep focus block', 'time' => Carbon::parse('today 7:00 PM')->format('g:i A')],
                ['title' => 'Quick review', 'time' => Carbon::parse('tomorrow 8:00 AM')->format('D g:i A')],
            ],
        ]);
    }
}
