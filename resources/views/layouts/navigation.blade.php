@php
    $links = [
        ['label' => 'Dashboard', 'route' => 'dashboard'],
        ['label' => 'Timer', 'route' => 'timer.index'],
        ['label' => 'Subjects', 'route' => 'subjects.index'],
        ['label' => 'History', 'route' => 'sessions.index'],
        ['label' => 'Analytics', 'route' => 'analytics.index'],
        ['label' => 'Goals', 'route' => 'goals.edit'],
    ];
@endphp

<nav x-data="{ open: false }" class="sticky top-0 z-30 border-b border-rose-100 bg-white/95 shadow-sm backdrop-blur">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex min-h-16 items-center justify-between gap-4">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                <span class="flex size-10 items-center justify-center rounded-lg bg-rose-500 text-lg font-bold text-white">ST</span>
                <span class="hidden text-lg font-bold text-slate-800 sm:block">Study Timer Tracker</span>
            </a>

            <div class="hidden items-center gap-1 lg:flex">
                @foreach ($links as $link)
                    <a href="{{ route($link['route']) }}" class="rounded-lg px-3 py-2 text-sm font-semibold transition {{ request()->routeIs($link['route']) ? 'bg-rose-100 text-rose-700' : 'text-slate-500 hover:bg-rose-50 hover:text-rose-700' }}">
                        {{ $link['label'] }}
                    </a>
                @endforeach
            </div>

            <div class="hidden items-center gap-3 lg:flex">
                <a href="{{ route('profile.edit') }}" class="text-sm font-medium text-slate-500 hover:text-rose-700">{{ Auth::user()->name }}</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-rose-600">Log out</button>
                </form>
            </div>

            <button @click="open = ! open" class="rounded-lg border border-rose-100 p-2 text-slate-500 lg:hidden">
                <span class="sr-only">Open menu</span>
                <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h16M4 12h16M4 17h16" />
                </svg>
            </button>
        </div>
    </div>

    <div x-show="open" x-transition class="border-t border-rose-100 bg-white px-4 py-4 lg:hidden">
        <div class="grid gap-2">
            @foreach ($links as $link)
                <a href="{{ route($link['route']) }}" class="rounded-lg px-3 py-2 text-sm font-semibold {{ request()->routeIs($link['route']) ? 'bg-rose-100 text-rose-700' : 'text-slate-600' }}">
                    {{ $link['label'] }}
                </a>
            @endforeach
            <a href="{{ route('profile.edit') }}" class="rounded-lg px-3 py-2 text-sm font-semibold text-slate-600">Profile</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="w-full rounded-lg bg-slate-900 px-3 py-2 text-left text-sm font-semibold text-white">Log out</button>
            </form>
        </div>
    </div>
</nav>
