<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $subjects = $request->user()->subjects()
            ->withSum('studySessions as total_minutes', 'duration_minutes')
            ->orderBy('name')
            ->get();

        return view('subjects.index', compact('subjects'));
    }

    public function create()
    {
        return view('subjects.create', ['subject' => new Subject()]);
    }

    public function store(Request $request)
    {
        $request->user()->subjects()->create($this->validatedSubject($request));

        return redirect()->route('subjects.index')->with('status', 'Subject created.');
    }

    public function show(Subject $subject)
    {
        abort(404);
    }

    public function edit(Subject $subject)
    {
        $this->authorizeSubject($subject);

        return view('subjects.edit', compact('subject'));
    }

    public function update(Request $request, Subject $subject)
    {
        $this->authorizeSubject($subject);
        $subject->update($this->validatedSubject($request));

        return redirect()->route('subjects.index')->with('status', 'Subject updated.');
    }

    public function destroy(Subject $subject)
    {
        $this->authorizeSubject($subject);
        $subject->delete();

        return redirect()->route('subjects.index')->with('status', 'Subject deleted. Past sessions were kept.');
    }

    private function validatedSubject(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'color' => ['required', 'string', 'max:20'],
            'weekly_goal_minutes' => ['required', 'integer', 'min:0', 'max:10080'],
        ]);
    }

    private function authorizeSubject(Subject $subject): void
    {
        abort_unless($subject->user_id === request()->user()->id, 403);
    }
}
