<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Study Timer Tracker') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-rose-50 font-sans text-slate-700">
        <main class="mx-auto flex min-h-screen max-w-6xl flex-col justify-center px-6 py-10">
            <nav class="mb-12 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="flex size-10 items-center justify-center rounded-lg bg-rose-500 text-lg font-bold text-white">ST</span>
                    <span class="text-lg font-bold text-slate-900">Study Timer Tracker</span>
                </div>
                <div class="flex gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="rounded-lg bg-rose-500 px-5 py-3 text-sm font-bold text-white hover:bg-rose-600">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="rounded-lg px-5 py-3 text-sm font-bold text-rose-700 hover:bg-white">Log in</a>
                        <a href="{{ route('register') }}" class="rounded-lg bg-rose-500 px-5 py-3 text-sm font-bold text-white hover:bg-rose-600">Register</a>
                    @endauth
                </div>
            </nav>

            <section class="grid items-center gap-10 lg:grid-cols-[1.05fr_.95fr]">
                <div>
                    <p class="text-sm font-bold uppercase tracking-wide text-rose-500">Student-friendly focus tracking</p>
                    <h1 class="mt-4 text-5xl font-bold leading-tight text-slate-950 sm:text-6xl">Track study time with calm, clear progress.</h1>
                    <p class="mt-5 max-w-2xl text-lg text-slate-600">Plan goals, run Pomodoro sessions, review history, and understand your study habits with simple charts.</p>
                    <div class="mt-8 flex flex-wrap gap-3">
                        <a href="{{ route('register') }}" class="rounded-lg bg-rose-500 px-6 py-3 font-bold text-white shadow-sm hover:bg-rose-600">Create account</a>
                        <a href="{{ route('login') }}" class="rounded-lg bg-white px-6 py-3 font-bold text-rose-700 ring-1 ring-rose-200 hover:bg-rose-50">I already have one</a>
                    </div>
                </div>

                <div class="rounded-lg border border-rose-100 bg-white p-6 shadow-sm">
                    <div class="mb-5 flex items-center justify-between">
                        <h2 class="text-xl font-bold text-slate-900">Today</h2>
                        <span class="rounded-lg bg-rose-100 px-3 py-1 text-sm font-bold text-rose-700">Focus mode</span>
                    </div>
                    <div class="rounded-lg bg-rose-50 p-6 text-center">
                        <p class="text-6xl font-bold text-slate-900">25:00</p>
                        <p class="mt-2 text-slate-500">Pomodoro ready</p>
                    </div>
                    <div class="mt-5 grid gap-3">
                        <div class="h-3 overflow-hidden rounded-full bg-rose-100"><div class="h-full w-2/3 rounded-full bg-rose-500"></div></div>
                        <p class="text-sm text-slate-500">Daily goal progress: 67%</p>
                    </div>
                </div>
            </section>
        </main>
    </body>
</html>
