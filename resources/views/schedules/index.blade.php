<x-app-layout>
    <x-slot name="header">
        <h1 class="text-3xl font-bold text-slate-900">Study schedule</h1>
        <p class="mt-1 text-slate-500">Plan your week, set subject targets, and establish a consistent routine.</p>
    </x-slot>

    @if (session('success'))
        <div class="mb-6 rounded-lg bg-emerald-50 p-4 text-sm font-semibold text-emerald-800 border border-emerald-100 shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-[.8fr_1.2fr]">
        <!-- Left Side: Add Schedule Form Card -->
        <aside class="h-fit rounded-xl border border-rose-100 bg-white p-6 shadow-sm">
            <h2 class="text-xl font-bold text-slate-900">Add schedule slot</h2>
            <form method="POST" action="{{ route('schedules.store') }}" class="mt-5 space-y-4">
                @csrf
                
                <label class="block">
                    <span class="text-sm font-semibold text-slate-600">Subject</span>
                    <select name="subject_id" class="mt-2 w-full rounded-lg border-rose-200 focus:border-rose-400 focus:ring-rose-400">
                        <option value="">Generic Focus Time</option>
                        @foreach ($subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="block">
                    <span class="text-sm font-semibold text-slate-600">Day of the Week</span>
                    <select name="day_of_week" required class="mt-2 w-full rounded-lg border-rose-200 focus:border-rose-400 focus:ring-rose-400">
                        <option value="Monday">Monday</option>
                        <option value="Tuesday">Tuesday</option>
                        <option value="Wednesday">Wednesday</option>
                        <option value="Thursday">Thursday</option>
                        <option value="Friday">Friday</option>
                        <option value="Saturday">Saturday</option>
                        <option value="Sunday">Sunday</option>
                    </select>
                </label>

                <div class="grid grid-cols-2 gap-3">
                    <label class="block">
                        <span class="text-sm font-semibold text-slate-600">Start Time</span>
                        <input type="time" name="start_time" value="{{ old('start_time') }}" required class="mt-2 w-full rounded-lg border-rose-200 focus:border-rose-400 focus:ring-rose-400">
                    </label>
                    <label class="block">
                        <span class="text-sm font-semibold text-slate-600">End Time</span>
                        <input type="time" name="end_time" value="{{ old('end_time') }}" required class="mt-2 w-full rounded-lg border-rose-200 focus:border-rose-400 focus:ring-rose-400">
                    </label>
                </div>
                <x-input-error :messages="$errors->get('start_time')" class="mt-2" />
                <x-input-error :messages="$errors->get('end_time')" class="mt-2" />

                <label class="block">
                    <span class="text-sm font-semibold text-slate-600">Study Goals / Notes</span>
                    <textarea name="notes" rows="3" placeholder="What are you focusing on?" class="mt-2 w-full rounded-lg border-rose-200 focus:border-rose-400 focus:ring-rose-400">{{ old('notes') }}</textarea>
                </label>

                <button class="w-full rounded-lg bg-rose-500 py-3 text-sm font-bold text-white shadow-sm hover:bg-rose-600 transition-colors">Add to schedule</button>
            </form>
        </aside>

        <!-- Right Side: Weekly Schedule Planner Grid -->
        <section class="space-y-6">
            @foreach ($days as $day)
                <div class="overflow-hidden rounded-xl border border-rose-100 bg-white shadow-sm hover:shadow-md transition-shadow duration-300">
                    <!-- Day Header Banner -->
                    <div class="bg-gradient-to-r from-rose-50 to-pink-50/30 px-5 py-4 border-b border-rose-50 flex items-center justify-between">
                        <h3 class="font-bold text-slate-800 text-lg flex items-center gap-2">
                            <span class="inline-block w-2.5 h-2.5 rounded-full bg-rose-400"></span>
                            {{ $day }}
                        </h3>
                        <span class="text-xs font-semibold text-rose-500 uppercase tracking-wider">
                            {{ isset($schedules[$day]) ? count($schedules[$day]) : 0 }} slots
                        </span>
                    </div>

                    <!-- Day Content: Scheduled Slots -->
                    <div class="p-5 divide-y divide-rose-50/50">
                        @if (isset($schedules[$day]) && count($schedules[$day]) > 0)
                            @foreach ($schedules[$day] as $slot)
                                <div class="py-4 first:pt-0 last:pb-0 flex flex-wrap items-center justify-between gap-4">
                                    <div class="flex items-start gap-4">
                                        <!-- Time Badge -->
                                        <div class="rounded-lg bg-rose-50/80 px-3 py-2 text-center min-w-[110px] border border-rose-100/50">
                                            <p class="text-xs font-bold text-rose-700">
                                                {{ \Carbon\Carbon::createFromFormat('H:i:s', $slot->start_time)->format('g:i A') }}
                                            </p>
                                            <p class="text-[10px] text-rose-400 font-medium mt-0.5">to</p>
                                            <p class="text-xs font-bold text-rose-700 mt-0.5">
                                                {{ \Carbon\Carbon::createFromFormat('H:i:s', $slot->end_time)->format('g:i A') }}
                                            </p>
                                        </div>

                                        <!-- Subject details -->
                                        <div>
                                            <div class="flex flex-wrap items-center gap-2">
                                                <span class="font-bold text-slate-800">
                                                    {{ $slot->subject ? $slot->subject->name : 'Generic Focus' }}
                                                </span>
                                                <span class="inline-block px-2.5 py-0.5 text-[10px] font-bold rounded-full {{ $slot->subject ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-600' }}">
                                                    {{ $slot->subject ? 'Subject Target' : 'Focus' }}
                                                </span>
                                            </div>
                                            @if ($slot->notes)
                                                <p class="text-sm text-slate-500 mt-1.5 leading-relaxed">{{ $slot->notes }}</p>
                                            @else
                                                <p class="text-xs text-slate-400/80 italic mt-1">No notes added.</p>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Quick Action Delete Button -->
                                    <form method="POST" action="{{ route('schedules.destroy', $slot) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="rounded-lg p-2 text-slate-400 hover:bg-rose-50 hover:text-rose-600 transition-colors" title="Delete slot">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        @else
                            <div class="py-2 text-center text-slate-400 italic text-sm">
                                No study sessions scheduled for {{ $day }}.
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </section>
    </div>
</x-app-layout>
