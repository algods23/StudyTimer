<x-app-layout>
    <x-slot name="header">
        <h1 class="text-3xl font-bold text-slate-900">Study timer</h1>
        <p class="mt-1 text-slate-500">Choose a subject, focus, then save the session automatically when you stop.</p>
    </x-slot>

    <div x-data="studyTimer()" class="grid gap-6 lg:grid-cols-[1.2fr_.8fr]">
        <section class="rounded-lg border border-rose-100 bg-white p-6 shadow-sm" :class="focusMode ? 'fixed inset-0 z-40 flex flex-col justify-center rounded-none border-0 bg-rose-50 p-6' : ''">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wide text-rose-500" x-text="modeLabel"></p>
                    <h2 class="text-2xl font-bold text-slate-900">Focus session</h2>
                </div>
                <button @click="focusMode = !focusMode" class="rounded-lg border border-rose-200 px-4 py-2 text-sm font-semibold text-rose-700 hover:bg-rose-50" x-text="focusMode ? 'Exit focus' : 'Focus mode'"></button>
            </div>

            <div class="my-10 text-center">
                <div class="text-7xl font-bold tabular-nums text-slate-900 sm:text-8xl" x-text="displayTime"></div>
                <p class="mt-3 text-slate-500" x-text="statusText"></p>
            </div>

            <div class="flex flex-wrap justify-center gap-3">
                <button @click="start" :disabled="running" class="rounded-lg bg-rose-500 px-6 py-3 font-bold text-white shadow-sm hover:bg-rose-600 disabled:opacity-40">Start</button>
                <button @click="pause" :disabled="!running" class="rounded-lg bg-white px-6 py-3 font-bold text-rose-700 ring-1 ring-rose-200 hover:bg-rose-50 disabled:opacity-40">Pause</button>
                <button @click="resume" :disabled="running || !startedAt" class="rounded-lg bg-white px-6 py-3 font-bold text-rose-700 ring-1 ring-rose-200 hover:bg-rose-50 disabled:opacity-40">Resume</button>
                <button @click="stop" :disabled="!startedAt" class="rounded-lg bg-slate-900 px-6 py-3 font-bold text-white hover:bg-slate-700 disabled:opacity-40">Stop & save</button>
            </div>
        </section>

        <aside class="rounded-lg border border-rose-100 bg-white p-6 shadow-sm">
            <h2 class="text-xl font-bold text-slate-900">Timer settings</h2>
            <div class="mt-5 space-y-5">
                <label class="block">
                    <span class="text-sm font-semibold text-slate-600">Subject</span>
                    <select x-model="subjectId" class="mt-2 w-full rounded-lg border-rose-200 focus:border-rose-400 focus:ring-rose-400">
                        <option value="">No subject</option>
                        @foreach ($subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="block">
                    <span class="text-sm font-semibold text-slate-600">Mode</span>
                    <select x-model="mode" @change="applyMode" class="mt-2 w-full rounded-lg border-rose-200 focus:border-rose-400 focus:ring-rose-400">
                        <option value="custom">Custom timer</option>
                        <option value="pomodoro">Pomodoro 25/5</option>
                    </select>
                </label>

                <div class="grid grid-cols-2 gap-3">
                    <label class="block">
                        <span class="text-sm font-semibold text-slate-600">Focus minutes</span>
                        <input type="number" min="1" max="240" x-model.number="focusMinutes" @input="resetDuration" class="mt-2 w-full rounded-lg border-rose-200 focus:border-rose-400 focus:ring-rose-400">
                    </label>
                    <label class="block">
                        <span class="text-sm font-semibold text-slate-600">Break minutes</span>
                        <input type="number" min="1" max="60" x-model.number="breakMinutes" class="mt-2 w-full rounded-lg border-rose-200 focus:border-rose-400 focus:ring-rose-400">
                    </label>
                </div>

                <label class="block">
                    <span class="text-sm font-semibold text-slate-600">Notes</span>
                    <textarea x-model="notes" rows="4" class="mt-2 w-full rounded-lg border-rose-200 focus:border-rose-400 focus:ring-rose-400" placeholder="What did you study?"></textarea>
                </label>

                <div x-show="message" class="rounded-lg bg-rose-50 p-4 text-sm font-semibold text-rose-700" x-text="message"></div>
            </div>
        </aside>
    </div>

    <script>
        function studyTimer() {
            return {
                mode: 'custom', focusMinutes: 25, breakMinutes: 5, remaining: 1500, running: false,
                interval: null, startedAt: null, subjectId: '', notes: '', message: '', focusMode: false,
                get displayTime() {
                    const minutes = String(Math.floor(this.remaining / 60)).padStart(2, '0');
                    const seconds = String(this.remaining % 60).padStart(2, '0');
                    return `${minutes}:${seconds}`;
                },
                get modeLabel() { return this.mode === 'pomodoro' ? `Pomodoro ${this.focusMinutes}/${this.breakMinutes}` : 'Custom timer'; },
                get statusText() { return this.running ? 'Timer is running' : (this.startedAt ? 'Paused and ready to resume' : 'Ready when you are'); },
                applyMode() { if (this.mode === 'pomodoro') { this.focusMinutes = 25; this.breakMinutes = 5; } this.resetDuration(); },
                resetDuration() { if (!this.running && !this.startedAt) this.remaining = Math.max(1, this.focusMinutes) * 60; },
                start() { this.startedAt = new Date(); this.remaining = Math.max(1, this.focusMinutes) * 60; this.tick(); },
                pause() { clearInterval(this.interval); this.running = false; },
                resume() { this.tick(); },
                tick() {
                    clearInterval(this.interval);
                    this.running = true;
                    this.interval = setInterval(() => {
                        if (this.remaining > 0) this.remaining--;
                        if (this.remaining === 0) this.stop();
                    }, 1000);
                },
                async stop() {
                    clearInterval(this.interval);
                    this.running = false;
                    if (!this.startedAt) return;
                    const endedAt = new Date();
                    const duration = Math.max(1, Math.round((endedAt - this.startedAt) / 60000));
                    const response = await fetch('{{ route('timer.sessions.store') }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                        body: JSON.stringify({ subject_id: this.subjectId || null, started_at: this.startedAt.toISOString(), ended_at: endedAt.toISOString(), duration_minutes: duration, mode: this.mode, notes: this.notes })
                    });
                    this.message = response.ok ? 'Session saved. Beautiful focus.' : 'Could not save yet. Please check your fields.';
                    this.startedAt = null;
                    this.resetDuration();
                }
            }
        }
    </script>
</x-app-layout>
