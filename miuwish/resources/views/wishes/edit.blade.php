<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-semibold">Изменить желание</h1>
    </x-slot>

    <div class="max-w-xl mx-auto px-4 sm:px-6 py-8">
        <form method="POST" action="{{ route('wishes.update', $wishlist) }}" enctype="multipart/form-data" class="rounded-2xl border border-line bg-white p-6">
            @include('wishes.partials.form')
        </form>
    </div>
</x-app-layout>
