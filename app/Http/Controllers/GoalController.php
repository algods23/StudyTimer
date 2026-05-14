<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GoalController extends Controller
{
    public function edit(Request $request)
    {
        $goal = $request->user()->goal()->firstOrCreate([]);
        $totals = [
            'daily' => $request->user()->studySessions()->whereDate('started_at', now())->sum('duration_minutes'),
            'weekly' => $request->user()->studySessions()->whereBetween('started_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('duration_minutes'),
            'monthly' => $request->user()->studySessions()->whereBetween('started_at', [now()->startOfMonth(), now()->endOfMonth()])->sum('duration_minutes'),
        ];

        return view('goals.edit', compact('goal', 'totals'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'daily_goal_minutes' => ['required', 'integer', 'min:0', 'max:1440'],
            'weekly_goal_minutes' => ['required', 'integer', 'min:0', 'max:10080'],
            'monthly_goal_minutes' => ['required', 'integer', 'min:0', 'max:44640'],
        ]);

        $request->user()->goal()->updateOrCreate(['user_id' => $request->user()->id], $data);

        return redirect()->route('goals.edit')->with('status', 'Goals updated.');
    }
}
