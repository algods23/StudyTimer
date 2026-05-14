<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Subjects</h1>
                <p class="mt-1 text-slate-500">Organize study time by subject and weekly goals.</p>
            </div>
            <a href="{{ route('subjects.create') }}" class="rounded-lg bg-rose-500 px-5 py-3 text-center text-sm font-bold text-white hover:bg-rose-600">Add subject</a>
        </div>
    </x-slot>

    <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
        @forelse ($subjects as $subject)
            @php $total = $subject->total_minutes ?? 0; $percent = $subject->weekly_goal_minutes ? min(100, round($total / $subject->weekly_goal_minutes * 100)) : 0; @endphp
            <article class="rounded-lg border border-rose-100 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <span class="inline-flex size-4 rounded-full" style="background: {{ $subject->color }}"></span>
                        <h2 class="mt-3 text-xl font-bold text-slate-900">{{ $subject->name }}</h2>
                        <p class="text-sm text-slate-500">{{ round($total / 60, 1) }} total hours</p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('subjects.edit', $subject) }}" class="rounded-lg border border-rose-200 px-3 py-2 text-sm font-bold text-rose-700 hover:bg-rose-50">Edit</a>
                        <form method="POST" action="{{ route('subjects.destroy', $subject) }}" onsubmit="return confirm('Delete this subject? Past sessions will stay in history.');">
                            @csrf
                            @method('DELETE')
                            <button class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-bold text-slate-500 hover:bg-slate-50">Delete</button>
                        </form>
                    </div>
                </div>
                <div class="mt-5">
                    <div class="mb-2 flex justify-between text-sm">
                        <span class="font-semibold text-slate-600">Weekly goal</span>
                        <span class="text-slate-400">{{ round($subject->weekly_goal_minutes / 60, 1) }}h</span>
                    </div>
                    <div class="h-3 overflow-hidden rounded-full bg-rose-100">
                        <div class="h-full rounded-full" style="width: {{ $percent }}%; background: {{ $subject->color }}"></div>
                    </div>
                </div>
            </article>
        @empty
            <div class="rounded-lg border border-rose-100 bg-white p-8 text-center shadow-sm md:col-span-2 xl:col-span-3">
                <h2 class="text-xl font-bold text-slate-900">No subjects yet</h2>
                <p class="mt-2 text-slate-500">Create your first subject to start tracking focus time.</p>
            </div>
        @endforelse
    </div>
</x-app-layout>
