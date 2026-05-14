<x-app-layout>
    <x-slot name="header">
        <h1 class="text-3xl font-bold text-slate-900">Analytics</h1>
        <p class="mt-1 text-slate-500">Patterns, comparisons, and subject distribution.</p>
    </x-slot>

    <div class="grid gap-6 xl:grid-cols-2">
        <section class="rounded-lg border border-rose-100 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-bold text-slate-900">Daily study trend</h2>
            <div class="mt-4 h-72"><canvas id="dailyTrend"></canvas></div>
        </section>
        <section class="rounded-lg border border-rose-100 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-bold text-slate-900">Weekly comparison</h2>
            <div class="mt-4 h-72"><canvas id="weeklyComparison"></canvas></div>
        </section>
        <section class="rounded-lg border border-rose-100 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-bold text-slate-900">Subject study distribution</h2>
            <div class="mt-4 h-72"><canvas id="subjectDistribution"></canvas></div>
        </section>
        <section class="rounded-lg border border-rose-100 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-bold text-slate-900">Goal completion statistics</h2>
            @php $monthPercent = $goal->monthly_goal_minutes ? min(100, round($monthMinutes / $goal->monthly_goal_minutes * 100)) : 0; @endphp
            <div class="mt-8 text-center">
                <p class="text-6xl font-bold text-rose-500">{{ $monthPercent }}%</p>
                <p class="mt-2 text-slate-500">{{ $monthMinutes }} of {{ $goal->monthly_goal_minutes }} monthly minutes</p>
            </div>
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const rose = '#f43f5e';
            new Chart(document.getElementById('dailyTrend'), { type: 'line', data: { labels: @json($dailyTrend->pluck('label')), datasets: [{ data: @json($dailyTrend->pluck('minutes')), borderColor: rose, backgroundColor: 'rgba(244,63,94,.12)', fill: true, tension: .35 }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } } });
            new Chart(document.getElementById('weeklyComparison'), { type: 'bar', data: { labels: @json($weeklyComparison->pluck('label')), datasets: [{ data: @json($weeklyComparison->pluck('minutes')), backgroundColor: '#fb7185', borderRadius: 8 }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } } });
            new Chart(document.getElementById('subjectDistribution'), { type: 'doughnut', data: { labels: @json($subjectDistribution->pluck('name')), datasets: [{ data: @json($subjectDistribution->pluck('total_minutes')), backgroundColor: @json($subjectDistribution->pluck('color')) }] }, options: { responsive: true, maintainAspectRatio: false } });
        });
    </script>
</x-app-layout>
