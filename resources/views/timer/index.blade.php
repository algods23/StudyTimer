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
                <button @click="resume" :disabled="running || (!startedAt && mode !== 'playlist')" class="rounded-lg bg-white px-6 py-3 font-bold text-rose-700 ring-1 ring-rose-200 hover:bg-rose-50 disabled:opacity-40">Resume</button>
                <button @click="stop" :disabled="!startedAt" class="rounded-lg bg-slate-900 px-6 py-3 font-bold text-white hover:bg-slate-700 disabled:opacity-40">Stop & save</button>
            </div>

            <!-- Active Playlist Timeline -->
            <div x-show="mode === 'playlist' && segments.length > 0" class="mt-8 border-t border-rose-100/50 pt-6">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3 text-left">Study Playlist Progress</p>
                <div class="flex items-center gap-2 overflow-x-auto pb-2">
                    <template x-for="(segment, idx) in segments" :key="idx">
                        <div class="flex items-center gap-1 shrink-0">
                            <!-- Segment Bubble -->
                            <div class="flex items-center gap-2 rounded-lg px-3 py-1.5 border transition-all duration-300"
                                 :class="idx === currentSegmentIndex 
                                         ? 'bg-rose-500 text-white border-rose-600 shadow-md ring-2 ring-rose-200' 
                                         : (idx < currentSegmentIndex 
                                            ? 'bg-emerald-50 text-emerald-700 border-emerald-200' 
                                            : 'bg-slate-50 text-slate-400 border-slate-200')">
                                
                                <span class="flex size-4 items-center justify-center rounded-full text-[9px] font-bold"
                                      :class="idx === currentSegmentIndex 
                                              ? 'bg-white text-rose-700' 
                                              : (idx < currentSegmentIndex 
                                                 ? 'bg-emerald-200 text-emerald-800' 
                                                 : 'bg-slate-200 text-slate-600')">
                                    <template x-if="idx < currentSegmentIndex">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="size-2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                        </svg>
                                    </template>
                                    <template x-if="idx >= currentSegmentIndex" x-text="idx + 1"></template>
                                </span>
                                
                                <span class="text-xs font-bold" x-text="segment.name"></span>
                                <span class="text-[10px] opacity-80" x-text="`${segment.duration}m`"></span>
                            </div>
                            
                            <!-- Connecting arrow -->
                            <div x-show="idx < segments.length - 1" class="text-slate-300">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                </svg>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </section>

        <aside class="rounded-lg border border-rose-100 bg-white p-6 shadow-sm">
            <h2 class="text-xl font-bold text-slate-900">Timer settings</h2>
            <div class="mt-5 space-y-5">
                <label class="block">
                    <span class="text-sm font-semibold text-slate-600">Mode</span>
                    <select x-model="mode" @change="applyMode" class="mt-2 w-full rounded-lg border-rose-200 focus:border-rose-400 focus:ring-rose-400">
                        <option value="custom">Custom timer</option>
                        <option value="pomodoro">Pomodoro 25/5</option>
                        <option value="playlist">Study Playlist (Multi-Segment)</option>
                    </select>
                </label>

                <!-- Standard Mode Inputs -->
                <div x-show="mode !== 'playlist'" class="space-y-5">
                    <label class="block">
                        <span class="text-sm font-semibold text-slate-600">Subject</span>
                        <select x-model="subjectId" class="mt-2 w-full rounded-lg border-rose-200 focus:border-rose-400 focus:ring-rose-400">
                            <option value="">No subject</option>
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
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
                </div>

                <!-- Playlist Mode Inputs -->
                <div x-show="mode === 'playlist'" class="space-y-4 text-left">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Playlist Builder</h3>
                    
                    <!-- Add Segment Form -->
                    <div class="rounded-xl border border-rose-100 bg-rose-50/50 p-4 space-y-3">
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <span class="text-xs font-semibold text-slate-500">Segment Type</span>
                                <select x-model="newType" class="mt-1 w-full rounded-lg border-rose-200 text-xs py-1.5 focus:border-rose-400 focus:ring-rose-400">
                                    <option value="subject">Study Subject</option>
                                    <option value="break">Rest / Break</option>
                                </select>
                            </div>
                            <div x-show="newType === 'subject'">
                                <span class="text-xs font-semibold text-slate-500">Subject</span>
                                <select id="new-subject-select" x-model="newSubjectId" class="mt-1 w-full rounded-lg border-rose-200 text-xs py-1.5 focus:border-rose-400 focus:ring-rose-400">
                                    @foreach ($subjects as $subject)
                                        <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                    @endforeach
                                    @if ($subjects->isEmpty())
                                        <option value="">Generic Focus</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div>
                            <span class="text-xs font-semibold text-slate-500">Duration (Minutes)</span>
                            <div class="mt-1 flex gap-2">
                                <input type="number" min="1" max="180" x-model.number="newDuration" class="w-full rounded-lg border-rose-200 text-xs py-1.5 focus:border-rose-400 focus:ring-rose-400">
                                <button type="button" @click="addSegment" class="rounded-lg bg-rose-500 px-3 py-1.5 text-xs font-bold text-white hover:bg-rose-600 transition-colors">Add</button>
                            </div>
                        </div>
                    </div>

                    <!-- Playlist Queue -->
                    <div class="space-y-2">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Queue</span>
                        <div class="max-h-[220px] overflow-y-auto space-y-2 pr-1">
                            <template x-for="(segment, index) in segments" :key="index">
                                <div class="flex items-center justify-between rounded-lg border border-rose-100 bg-white p-3 shadow-xs">
                                    <div class="flex items-center gap-2">
                                        <span class="flex size-6 items-center justify-center rounded-full text-xs font-bold"
                                              :class="segment.type === 'subject' ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-600'"
                                              x-text="index + 1"></span>
                                        <div>
                                            <p class="text-xs font-bold text-slate-800" x-text="segment.name"></p>
                                            <p class="text-[10px] text-slate-400" x-text="segment.type === 'subject' ? 'Study session' : 'Break'"></p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-bold text-rose-600" x-text="`${segment.duration}m`"></span>
                                        <button type="button" @click="removeSegment(index)" class="text-slate-400 hover:text-rose-600 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </template>
                            <div x-show="segments.length === 0" class="text-center py-4 text-xs italic text-slate-400">
                                Playlist is empty. Add study blocks above!
                            </div>
                        </div>
                    </div>
                </div>

                <div x-show="message" class="rounded-lg bg-rose-50 p-4 text-sm font-semibold text-rose-700" x-text="message"></div>
            </div>
        </aside>
    </div>

    <script>
        function studyTimer() {
            return {
                mode: 'custom', focusMinutes: 25, breakMinutes: 5, remaining: 1500, running: false,
                interval: null, startedAt: null, subjectId: '', notes: '', message: '', focusMode: false,
                
                // Playlist State
                segments: [
                    @php
                        $math = $subjects->firstWhere('name', 'Math') ?? $subjects->firstWhere('name', 'math');
                        $science = $subjects->firstWhere('name', 'Science') ?? $subjects->firstWhere('name', 'science');
                        $english = $subjects->firstWhere('name', 'English') ?? $subjects->firstWhere('name', 'english');
                    @endphp
                    { type: 'subject', id: '{{ $math ? $math->id : '' }}', name: '{{ $math ? $math->name : 'Math' }}', duration: 10 },
                    { type: 'break', id: '', name: 'Break', duration: 5 },
                    { type: 'subject', id: '{{ $science ? $science->id : '' }}', name: '{{ $science ? $science->name : 'Science' }}', duration: 10 },
                    { type: 'break', id: '', name: 'Break', duration: 5 },
                    { type: 'subject', id: '{{ $english ? $english->id : '' }}', name: '{{ $english ? $english->name : 'English' }}', duration: 15 }
                ],
                currentSegmentIndex: 0,
                segmentRemaining: 600, // Pre-populated first segment Math: 10 mins (600s)
                
                // Form input state for adding segment
                newType: 'subject',
                newSubjectId: '{{ $subjects->first() ? $subjects->first()->id : '' }}',
                newDuration: 10,
                
                get displayTime() {
                    const totalSeconds = this.mode === 'playlist' ? this.segmentRemaining : this.remaining;
                    const minutes = String(Math.floor(totalSeconds / 60)).padStart(2, '0');
                    const seconds = String(totalSeconds % 60).padStart(2, '0');
                    return `${minutes}:${seconds}`;
                },
                get modeLabel() {
                    if (this.mode === 'playlist') {
                        const current = this.segments[this.currentSegmentIndex];
                        return current ? `Playlist Slot: ${current.name} (${this.currentSegmentIndex + 1}/${this.segments.length})` : 'Study Playlist';
                    }
                    return this.mode === 'pomodoro' ? `Pomodoro ${this.focusMinutes}/${this.breakMinutes}` : 'Custom timer';
                },
                get statusText() {
                    return this.running 
                        ? 'Timer is running' 
                        : (this.startedAt ? 'Paused and ready to resume' : 'Ready when you are');
                },
                applyMode() {
                    if (this.mode === 'pomodoro') {
                        this.focusMinutes = 25;
                        this.breakMinutes = 5;
                    }
                    this.currentSegmentIndex = 0;
                    this.resetDuration();
                },
                resetDuration() {
                    if (this.mode === 'playlist') {
                        if (this.segments.length > 0) {
                            this.segmentRemaining = this.segments[this.currentSegmentIndex].duration * 60;
                        } else {
                            this.segmentRemaining = 0;
                        }
                    } else {
                        if (!this.running && !this.startedAt) this.remaining = Math.max(1, this.focusMinutes) * 60;
                    }
                },
                start() {
                    this.startedAt = new Date();
                    if (this.mode === 'playlist') {
                        this.resetDuration();
                    } else {
                        this.remaining = Math.max(1, this.focusMinutes) * 60;
                    }
                    this.tick();
                },
                pause() {
                    clearInterval(this.interval);
                    this.running = false;
                },
                resume() {
                    this.tick();
                },
                tick() {
                    clearInterval(this.interval);
                    this.running = true;
                    this.interval = setInterval(() => {
                        if (this.mode === 'playlist') {
                            if (this.segmentRemaining > 0) {
                                this.segmentRemaining--;
                            }
                            if (this.segmentRemaining === 0) {
                                this.nextSegment();
                            }
                        } else {
                            if (this.remaining > 0) this.remaining--;
                            if (this.remaining === 0) this.stop();
                        }
                    }, 1000);
                },
                async nextSegment() {
                    clearInterval(this.interval);
                    this.running = false;
                    
                    // Audio chime
                    try {
                        const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                        const oscillator = audioCtx.createOscillator();
                        const gainNode = audioCtx.createGain();
                        oscillator.connect(gainNode);
                        gainNode.connect(audioCtx.destination);
                        oscillator.type = 'sine';
                        oscillator.frequency.setValueAtTime(587.33, audioCtx.currentTime);
                        gainNode.gain.setValueAtTime(0.15, audioCtx.currentTime);
                        oscillator.start();
                        gainNode.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.8);
                        oscillator.stop(audioCtx.currentTime + 0.8);
                    } catch (e) {
                        console.log("Audio alert blocked by browser autocomplete/interaction rules.");
                    }

                    // Save finished study slot
                    const current = this.segments[this.currentSegmentIndex];
                    if (current && current.type === 'subject') {
                        try {
                            const segStartedAt = new Date(Date.now() - (current.duration * 60 * 1000));
                            const segEndedAt = new Date();
                            await fetch('{{ route('timer.sessions.store') }}', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                                body: JSON.stringify({
                                    subject_id: current.id || null,
                                    started_at: segStartedAt.toISOString(),
                                    ended_at: segEndedAt.toISOString(),
                                    duration_minutes: current.duration,
                                    mode: 'playlist',
                                    notes: `Completed part of multi-segment study playlist: ${current.name} (${current.duration} mins)`
                                })
                            });
                        } catch (err) {
                            console.error("Failed to automatically save playlist segment:", err);
                        }
                    }

                    // Advance
                    if (this.currentSegmentIndex < this.segments.length - 1) {
                        this.currentSegmentIndex++;
                        this.startedAt = new Date(); // Reset segment start time
                        this.segmentRemaining = this.segments[this.currentSegmentIndex].duration * 60;
                        this.tick();
                    } else {
                        // End of playlist
                        this.message = 'Congratulations! You completed your study playlist.';
                        this.startedAt = null;
                        this.currentSegmentIndex = 0;
                        this.resetDuration();
                    }
                },
                async stop() {
                    clearInterval(this.interval);
                    this.running = false;
                    if (this.mode === 'playlist') {
                        const current = this.segments[this.currentSegmentIndex];
                        if (this.startedAt && current && current.type === 'subject') {
                            const endedAt = new Date();
                            const elapsedMinutes = Math.max(1, Math.round((endedAt - this.startedAt) / 60000));
                            try {
                                await fetch('{{ route('timer.sessions.store') }}', {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                                    body: JSON.stringify({
                                        subject_id: current.id || null,
                                        started_at: this.startedAt.toISOString(),
                                        ended_at: endedAt.toISOString(),
                                        duration_minutes: elapsedMinutes,
                                        mode: 'playlist',
                                        notes: `Study playlist stopped early. Partially completed: ${current.name} (${elapsedMinutes} mins)`
                                    })
                                });
                            } catch (err) {
                                console.error("Failed to save partial segment:", err);
                            }
                        }
                        this.message = 'Study playlist stopped.';
                        this.startedAt = null;
                        this.currentSegmentIndex = 0;
                        this.resetDuration();
                    } else {
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
                },
                addSegment() {
                    let name = 'Break';
                    if (this.newType === 'subject') {
                        const selectEl = document.getElementById('new-subject-select');
                        name = selectEl ? selectEl.options[selectEl.selectedIndex].text : 'Generic Focus';
                    }
                    this.segments.push({
                        type: this.newType,
                        id: this.newType === 'subject' ? this.newSubjectId : '',
                        name: name,
                        duration: Math.max(1, parseInt(this.newDuration || 10))
                    });
                    if (this.mode === 'playlist' && !this.running && this.currentSegmentIndex === 0) {
                        this.resetDuration();
                    }
                },
                removeSegment(index) {
                    this.segments.splice(index, 1);
                    if (this.mode === 'playlist' && !this.running && this.currentSegmentIndex === 0) {
                        this.resetDuration();
                    }
                }
            }
        }
    </script>
</x-app-layout>
