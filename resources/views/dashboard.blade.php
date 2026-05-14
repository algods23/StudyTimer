<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-rose-500">Welcome back</p>
                <h1 class="text-3xl font-bold text-slate-900">Today's study dashboard</h1>
            </div>
            <a href="{{ route('timer.index') }}" class="rounded-lg bg-rose-500 px-5 py-3 text-center text-sm font-bold text-white shadow-sm transition hover:bg-rose-600">Start studying</a>
        </div>
    </x-slot>

    <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
        @foreach ([
            ['label' => 'Today', 'value' => round($todayMinutes / 60, 1).' hrs', 'hint' => $todayMinutes.' minutes logged'],
            ['label' => 'This week', 'value' => round($weekMinutes / 60, 1).' hrs', 'hint' => $goal->weekly_goal_minutes.' minute goal'],
            ['label' => 'This month', 'value' => round($monthMinutes / 60, 1).' hrs', 'hint' => $goal->monthly_goal_minutes.' minute goal'],
            ['label' => 'Goal complete', 'value' => $goalPercent.'%', 'hint' => 'Daily target progress'],
        ] as $card)
            <div class="rounded-lg border border-rose-100 bg-white p-5 shadow-sm">
                <p class="text-sm font-semibold text-slate-500">{{ $card['label'] }}</p>
                <p class="mt-3 text-3xl font-bold text-slate-900">{{ $card['value'] }}</p>
                <p class="mt-1 text-sm text-slate-400">{{ $card['hint'] }}</p>
            </div>
        @endforeach
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-3">
        <section class="rounded-lg border border-rose-100 bg-white p-5 shadow-sm xl:col-span-2">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-bold text-slate-900">Weekly study progress</h2>
                <span class="rounded-lg bg-rose-50 px-3 py-1 text-sm font-semibold text-rose-700">{{ $goalPercent }}%</span>
            </div>
            <div class="mb-5 h-3 overflow-hidden rounded-full bg-rose-100">
                <div class="h-full rounded-full bg-rose-500" style="width: {{ $goalPercent }}%"></div>
            </div>
            <div class="h-72">
                <canvas id="dailyTrendChart"></canvas>
            </div>
        </section>

        <section class="rounded-lg border border-rose-100 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-bold text-slate-900">Subject breakdown</h2>
            <div class="mt-4 space-y-4">
                @forelse ($subjects as $subject)
                    @php $percent = $subject->weekly_goal_minutes ? min(100, round(($subject->total_minutes ?? 0) / $subject->weekly_goal_minutes * 100)) : 0; @endphp
                    <div>
                        <div class="mb-2 flex items-center justify-between gap-3 text-sm">
                            <span class="flex items-center gap-2 font-semibold text-slate-700"><span class="size-3 rounded-full" style="background: {{ $subject->color }}"></span>{{ $subject->name }}</span>
                            <span class="text-slate-400">{{ round(($subject->total_minutes ?? 0) / 60, 1) }}h</span>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-rose-100">
                            <div class="h-full rounded-full" style="width: {{ $percent }}%; background: {{ $subject->color }}"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Add subjects to see your study balance.</p>
                @endforelse
            </div>
        </section>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <section class="rounded-lg border border-rose-100 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-bold text-slate-900">Upcoming study sessions</h2>
            <div class="mt-4 grid gap-3">
                @foreach ($upcomingSessions as $session)
                    <div class="rounded-lg bg-rose-50 p-4">
                        <p class="font-semibold text-slate-800">{{ $session['title'] }}</p>
                        <p class="text-sm text-rose-600">{{ $session['time'] }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="rounded-lg border border-rose-100 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-bold text-slate-900">Notifications</h2>
            <div class="mt-4 grid gap-3">
                @forelse ($notifications as $notification)
                    <div class="flex items-start justify-between gap-3 rounded-lg bg-white p-4 ring-1 ring-rose-100">
                        <div>
                            <p class="font-semibold text-slate-800">{{ $notification->title }}</p>
                            <p class="text-sm text-slate-500">{{ $notification->message }}</p>
                        </div>
                        @unless ($notification->read_at)
                            <form method="POST" action="{{ route('notifications.read', $notification) }}">
                                @csrf
                                @method('PATCH')
                                <button class="text-sm font-semibold text-rose-600">Read</button>
                            </form>
                        @endunless
                    </div>
                @empty
                    <p class="text-sm text-slate-500">You are all caught up.</p>
                @endforelse
            </div>
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            new Chart(document.getElementById('dailyTrendChart'), {
                type: 'line',
                data: {
                    labels: @json($dailyTrend->pluck('label')),
                    datasets: [{ label: 'Minutes', data: @json($dailyTrend->pluck('minutes')), borderColor: '#f43f5e', backgroundColor: 'rgba(244, 63, 94, .12)', tension: .35, fill: true }]
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
            });
        });
    </script>
</x-app-layout>
