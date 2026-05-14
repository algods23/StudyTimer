<x-app-layout>
    <x-slot name="header">
        <h1 class="text-3xl font-bold text-slate-900">Edit subject</h1>
    </x-slot>

    @include('subjects.partials.form', ['action' => route('subjects.update', $subject), 'method' => 'PUT'])
</x-app-layout>
