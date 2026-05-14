<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();
        $goal = $user->goal()->firstOrCreate([]);

        $dailyTrend = collect(range(13, 0))->map(function ($daysAgo) use ($user) {
            $date = now()->subDays($daysAgo);

            return ['label' => $date->format('M j'), 'minutes' => (int) $user->studySessions()->whereDate('started_at', $date)->sum('duration_minutes')];
        });

        $weeklyComparison = collect(range(5, 0))->map(function ($weeksAgo) use ($user) {
            $start = now()->subWeeks($weeksAgo)->startOfWeek();
            $end = $start->copy()->endOfWeek();

            return ['label' => $start->format('M j'), 'minutes' => (int) $user->studySessions()->whereBetween('started_at', [$start, $end])->sum('duration_minutes')];
        });

        $subjectDistribution = $user->subjects()->withSum('studySessions as total_minutes', 'duration_minutes')->orderBy('name')->get();
        $monthMinutes = (int) $user->studySessions()->whereBetween('started_at', [now()->startOfMonth(), now()->endOfMonth()])->sum('duration_minutes');

        return view('analytics.index', compact('dailyTrend', 'weeklyComparison', 'subjectDistribution', 'goal', 'monthMinutes'));
    }
}
