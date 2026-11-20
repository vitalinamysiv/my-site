<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-semibold">Профиль</h1>
    </x-slot>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 py-8 space-y-6">
        <div class="rounded-2xl border border-line bg-white p-6">
            @include('profile.partials.update-profile-information-form')
        </div>

        <div class="rounded-2xl border border-line bg-white p-6">
            @include('profile.partials.update-password-form')
        </div>

        <div class="rounded-2xl border border-line bg-white p-6">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</x-app-layout>
