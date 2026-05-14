<form method="POST" action="{{ $action }}" class="max-w-2xl rounded-lg border border-rose-100 bg-white p-6 shadow-sm">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="space-y-5">
        <label class="block">
            <span class="text-sm font-semibold text-slate-600">Subject name</span>
            <input name="name" value="{{ old('name', $subject->name) }}" class="mt-2 w-full rounded-lg border-rose-200 focus:border-rose-400 focus:ring-rose-400" required>
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </label>

        <label class="block">
            <span class="text-sm font-semibold text-slate-600">Color tag</span>
            <input type="color" name="color" value="{{ old('color', $subject->color ?: '#fb7185') }}" class="mt-2 h-12 w-24 rounded-lg border border-rose-200 bg-white p-1">
            <x-input-error :messages="$errors->get('color')" class="mt-2" />
        </label>

        <label class="block">
            <span class="text-sm font-semibold text-slate-600">Goal hours per week</span>
            <input type="number" min="0" step="0.25" name="weekly_goal_hours" value="{{ old('weekly_goal_hours', $subject->weekly_goal_minutes ? $subject->weekly_goal_minutes / 60 : 8) }}" class="mt-2 w-full rounded-lg border-rose-200 focus:border-rose-400 focus:ring-rose-400" oninput="document.getElementById('weekly_goal_minutes').value = Math.round(this.value * 60)">
            <input id="weekly_goal_minutes" type="hidden" name="weekly_goal_minutes" value="{{ old('weekly_goal_minutes', $subject->weekly_goal_minutes ?: 480) }}">
            <x-input-error :messages="$errors->get('weekly_goal_minutes')" class="mt-2" />
        </label>
    </div>

    <div class="mt-6 flex gap-3">
        <button class="rounded-lg bg-rose-500 px-5 py-3 text-sm font-bold text-white hover:bg-rose-600">Save subject</button>
        <a href="{{ route('subjects.index') }}" class="rounded-lg border border-rose-200 px-5 py-3 text-sm font-bold text-rose-700 hover:bg-rose-50">Cancel</a>
    </div>
</form>
