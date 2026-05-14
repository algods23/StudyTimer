<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SessionHistoryController extends Controller
{
    public function __invoke(Request $request)
    {
        $query = $request->user()->studySessions()->with('subject')->latest('started_at');

        if ($request->filter === 'today') {
            $query->whereDate('started_at', now());
        } elseif ($request->filter === 'week') {
            $query->whereBetween('started_at', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($request->filter === 'month') {
            $query->whereBetween('started_at', [now()->startOfMonth(), now()->endOfMonth()]);
        }

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->integer('subject_id'));
        }

        return view('sessions.index', [
            'sessions' => $query->paginate(12)->withQueryString(),
            'subjects' => $request->user()->subjects()->orderBy('name')->get(),
            'activeFilter' => $request->filter,
        ]);
    }
}
