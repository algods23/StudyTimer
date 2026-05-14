<x-app-layout>
    <x-slot name="header">
        <h1 class="text-3xl font-bold text-slate-900">Study goals</h1>
        <p class="mt-1 text-slate-500">Set targets and watch your progress fill up.</p>
    </x-slot>

    <div class="grid gap-6 lg:grid-cols-[.85fr_1.15fr]">
        <form method="POST" action="{{ route('goals.update') }}" class="rounded-lg border border-rose-100 bg-white p-6 shadow-sm">
            @csrf
            @method('PUT')
            <div class="space-y-5">
                @foreach ([['daily_goal_minutes', 'Daily goal'], ['weekly_goal_minutes', 'Weekly goal'], ['monthly_goal_minutes', 'Monthly goal']] as [$field, $label])
                    <label class="block">
                        <span class="text-sm font-semibold text-slate-600">{{ $label }} in minutes</span>
                        <input type="number" min="0" name="{{ $field }}" value="{{ old($field, $goal->{$field}) }}" class="mt-2 w-full rounded-lg border-rose-200 focus:border-rose-400 focus:ring-rose-400">
                        <x-input-error :messages="$errors->get($field)" class="mt-2" />
                    </label>
                @endforeach
            </div>
            <button class="mt-6 rounded-lg bg-rose-500 px-5 py-3 text-sm font-bold text-white hover:bg-rose-600">Save goals</button>
        </form>

        <section class="rounded-lg border border-rose-100 bg-white p-6 shadow-sm">
            <h2 class="text-xl font-bold text-slate-900">Current progress</h2>
            <div class="mt-5 space-y-5">
                @foreach ([['Daily', $totals['daily'], $goal->daily_goal_minutes], ['Weekly', $totals['weekly'], $goal->weekly_goal_minutes], ['Monthly', $totals['monthly'], $goal->monthly_goal_minutes]] as [$label, $current, $target])
                    @php $percent = $target ? min(100, round($current / $target * 100)) : 0; @endphp
                    <div>
                        <div class="mb-2 flex justify-between text-sm">
                            <span class="font-semibold text-slate-700">{{ $label }}</span>
                            <span class="text-slate-400">{{ $current }} / {{ $target }} min</span>
                        </div>
                        <div class="h-3 overflow-hidden rounded-full bg-rose-100">
                            <div class="h-full rounded-full bg-rose-500" style="width: {{ $percent }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    </div>
</x-app-layout>
