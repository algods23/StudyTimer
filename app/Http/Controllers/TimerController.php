<?php

namespace App\Http\Controllers;

use App\Models\StudySession;
use Illuminate\Http\Request;

class TimerController extends Controller
{
    public function index(Request $request)
    {
        return view('timer.index', [
            'subjects' => $request->user()->subjects()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'subject_id' => ['nullable', 'exists:subjects,id'],
            'started_at' => ['required', 'date'],
            'ended_at' => ['required', 'date', 'after_or_equal:started_at'],
            'duration_minutes' => ['required', 'integer', 'min:1'],
            'mode' => ['required', 'string', 'max:40'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        if ($data['subject_id'] && ! $request->user()->subjects()->whereKey($data['subject_id'])->exists()) {
            abort(403);
        }

        $session = StudySession::create($data + ['user_id' => $request->user()->id]);

        $request->user()->studyNotifications()->create([
            'title' => 'Study session saved',
            'message' => 'Great work. '.$session->duration_minutes.' minutes were added to your progress.',
            'type' => 'success',
        ]);

        return response()->json(['message' => 'Session saved', 'session_id' => $session->id]);
    }
}
