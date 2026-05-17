<x-app-layout>
    <x-slot name="header">
        <h1 class="text-3xl font-bold text-slate-900">Study goals</h1>
        <p class="mt-1 text-slate-500">Set targets and watch your progress fill up.</p>
    </x-slot>

    <div x-data="{
        daily: {{ old('daily_goal_minutes', $goal->daily_goal_minutes) ?? 0 }},
        weekly: {{ old('weekly_goal_minutes', $goal->weekly_goal_minutes) ?? 0 }},
        monthly: {{ old('monthly_goal_minutes', $goal->monthly_goal_minutes) ?? 0 }},
        currentDaily: {{ $totals['daily'] }},
        currentWeekly: {{ $totals['weekly'] }},
        currentMonthly: {{ $totals['monthly'] }},
        get dailyPercent() { return this.daily ? Math.min(100, Math.round((this.currentDaily / this.daily) * 100)) : 0; },
        get weeklyPercent() { return this.weekly ? Math.min(100, Math.round((this.currentWeekly / this.weekly) * 100)) : 0; },
        get monthlyPercent() { return this.monthly ? Math.min(100, Math.round((this.currentMonthly / this.monthly) * 100)) : 0; }
    }" class="grid gap-6 lg:grid-cols-[.85fr_1.15fr]">
        <form method="POST" action="{{ route('goals.update') }}" class="rounded-lg border border-rose-100 bg-white p-6 shadow-sm">
            @csrf
            @method('PUT')
            <div class="space-y-5">
                <!-- Daily Goal -->
                <label class="block">
                    <span class="text-sm font-semibold text-slate-600">Daily goal in minutes</span>
                    <input type="number" min="0" name="daily_goal_minutes" x-model.number="daily" @input="weekly = Math.round((daily || 0) * 5); monthly = Math.round((daily || 0) * 20);" class="mt-2 w-full rounded-lg border-rose-200 focus:border-rose-400 focus:ring-rose-400">
                    <x-input-error :messages="$errors->get('daily_goal_minutes')" class="mt-2" />
                </label>

                <!-- Weekly Goal -->
                <label class="block">
                    <span class="text-sm font-semibold text-slate-600">Weekly goal in minutes</span>
                    <input type="number" min="0" name="weekly_goal_minutes" x-model.number="weekly" @input="daily = Math.round((weekly || 0) / 5); monthly = Math.round((weekly || 0) * 4);" class="mt-2 w-full rounded-lg border-rose-200 focus:border-rose-400 focus:ring-rose-400">
                    <x-input-error :messages="$errors->get('weekly_goal_minutes')" class="mt-2" />
                </label>

                <!-- Monthly Goal -->
                <label class="block">
                    <span class="text-sm font-semibold text-slate-600">Monthly goal in minutes</span>
                    <input type="number" min="0" name="monthly_goal_minutes" x-model.number="monthly" @input="daily = Math.round((monthly || 0) / 20); weekly = Math.round((monthly || 0) / 4);" class="mt-2 w-full rounded-lg border-rose-200 focus:border-rose-400 focus:ring-rose-400">
                    <x-input-error :messages="$errors->get('monthly_goal_minutes')" class="mt-2" />
                </label>
            </div>
            <button class="mt-6 rounded-lg bg-rose-500 px-5 py-3 text-sm font-bold text-white hover:bg-rose-600">Save goals</button>
        </form>

        <section class="rounded-lg border border-rose-100 bg-white p-6 shadow-sm">
            <h2 class="text-xl font-bold text-slate-900">Current progress</h2>
            <div class="mt-5 space-y-5">
                <!-- Daily -->
                <div>
                    <div class="mb-2 flex justify-between text-sm">
                        <span class="font-semibold text-slate-700">Daily</span>
                        <span class="text-slate-400"><span x-text="currentDaily"></span> / <span x-text="daily || 0"></span> min</span>
                    </div>
                    <div class="h-3 overflow-hidden rounded-full bg-rose-100">
                        <div class="h-full rounded-full bg-rose-500 transition-all duration-300" :style="`width: ${dailyPercent}%`"></div>
                    </div>
                </div>

                <!-- Weekly -->
                <div>
                    <div class="mb-2 flex justify-between text-sm">
                        <span class="font-semibold text-slate-700">Weekly</span>
                        <span class="text-slate-400"><span x-text="currentWeekly"></span> / <span x-text="weekly || 0"></span> min</span>
                    </div>
                    <div class="h-3 overflow-hidden rounded-full bg-rose-100">
                        <div class="h-full rounded-full bg-rose-500 transition-all duration-300" :style="`width: ${weeklyPercent}%`"></div>
                    </div>
                </div>

                <!-- Monthly -->
                <div>
                    <div class="mb-2 flex justify-between text-sm">
                        <span class="font-semibold text-slate-700">Monthly</span>
                        <span class="text-slate-400"><span x-text="currentMonthly"></span> / <span x-text="monthly || 0"></span> min</span>
                    </div>
                    <div class="h-3 overflow-hidden rounded-full bg-rose-100">
                        <div class="h-full rounded-full bg-rose-500 transition-all duration-300" :style="`width: ${monthlyPercent}%`"></div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
