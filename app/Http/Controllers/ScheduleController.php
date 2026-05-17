<?php

namespace App\Http\Controllers;

use App\Models\StudySchedule;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        
        $schedules = $request->user()->studySchedules()
            ->with('subject')
            ->orderBy('start_time')
            ->get()
            ->groupBy('day_of_week');

        $subjects = $request->user()->subjects()->orderBy('name')->get();

        return view('schedules.index', compact('days', 'schedules', 'subjects'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'subject_id' => ['nullable', 'exists:subjects,id'],
            'day_of_week' => ['required', 'string', 'in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        // Security check: Make sure subject belongs to user
        if ($data['subject_id'] && !$request->user()->subjects()->whereKey($data['subject_id'])->exists()) {
            abort(403);
        }

        // Conflict check: check if there is an overlapping slot for the same user on the same day
        $conflict = $request->user()->studySchedules()
            ->where('day_of_week', $data['day_of_week'])
            ->where(function ($query) use ($data) {
                $query->where('start_time', '<', $data['end_time'])
                      ->where('end_time', '>', $data['start_time']);
            })
            ->first();

        if ($conflict) {
            $existingSubject = $conflict->subject ? $conflict->subject->name : 'Generic Focus';
            $existingStart = \Carbon\Carbon::createFromFormat('H:i:s', $conflict->start_time)->format('g:i A');
            $existingEnd = \Carbon\Carbon::createFromFormat('H:i:s', $conflict->end_time)->format('g:i A');
            
            return back()->withErrors([
                'start_time' => "This slot conflicts with {$existingSubject} ({$existingStart} - {$existingEnd})."
            ])->withInput();
        }

        $request->user()->studySchedules()->create($data);

        return redirect()->route('schedules.index')->with('success', 'Schedule slot created successfully.');
    }

    public function destroy(Request $request, StudySchedule $schedule)
    {
        if ($schedule->user_id !== $request->user()->id) {
            abort(403);
        }

        $schedule->delete();

        return redirect()->route('schedules.index')->with('success', 'Schedule slot deleted successfully.');
    }
}
