<x-app-layout>
    <x-slot name="header">
        <h1 class="text-3xl font-bold text-slate-900">Session history</h1>
        <p class="mt-1 text-slate-500">Review what you studied, when, and for how long.</p>
    </x-slot>

    <form method="GET" class="mb-6 grid gap-3 rounded-lg border border-rose-100 bg-white p-4 shadow-sm md:grid-cols-[1fr_1fr_auto]">
        <select name="filter" class="rounded-lg border-rose-200 focus:border-rose-400 focus:ring-rose-400">
            <option value="">All time</option>
            <option value="today" @selected($activeFilter === 'today')>Today</option>
            <option value="week" @selected($activeFilter === 'week')>This week</option>
            <option value="month" @selected($activeFilter === 'month')>This month</option>
        </select>
        <select name="subject_id" class="rounded-lg border-rose-200 focus:border-rose-400 focus:ring-rose-400">
            <option value="">All subjects</option>
            @foreach ($subjects as $subject)
                <option value="{{ $subject->id }}" @selected(request('subject_id') == $subject->id)>{{ $subject->name }}</option>
            @endforeach
        </select>
        <button class="rounded-lg bg-rose-500 px-5 py-3 text-sm font-bold text-white hover:bg-rose-600">Filter</button>
    </form>

    <div class="overflow-hidden rounded-lg border border-rose-100 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-rose-100">
                <thead class="bg-rose-50 text-left text-sm font-bold text-slate-600">
                    <tr>
                        <th class="px-4 py-3">Subject</th>
                        <th class="px-4 py-3">Date</th>
                        <th class="px-4 py-3">Start</th>
                        <th class="px-4 py-3">End</th>
                        <th class="px-4 py-3">Duration</th>
                        <th class="px-4 py-3">Notes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-rose-50 text-sm">
                    @forelse ($sessions as $session)
                        <tr>
                            <td class="px-4 py-3 font-semibold text-slate-800">{{ $session->subject?->name ?? 'No subject' }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $session->started_at->format('M j, Y') }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $session->started_at->format('g:i A') }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $session->ended_at->format('g:i A') }}</td>
                            <td class="px-4 py-3 font-semibold text-rose-600">{{ $session->duration_minutes }} min</td>
                            <td class="px-4 py-3 text-slate-500">{{ $session->notes ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-10 text-center text-slate-500">No sessions match this filter.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-5">{{ $sessions->links() }}</div>
</x-app-layout>
