<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h1 class="text-2xl font-semibold">Мои желания</h1>
            <div class="flex flex-wrap gap-2">
                <x-copy-link :url="route('profiles.show', Auth::user())" />
                <a href="{{ route('wishes.create') }}" class="rounded-full bg-blush px-4 py-2 text-sm font-medium">Добавить</a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-8">
        @include('wishes.partials.filters')

        @if ($wishlists->isEmpty())
            @include('wishes.partials.empty', ['message' => 'Пока желаний нет. Нажмите «Добавить».'])
        @else
            <div class="grid items-start gap-4 sm:grid-cols-2">
                @foreach ($wishlists as $wishlist)
                    @include('wishes.partials.card', ['wishlist' => $wishlist, 'showStatus' => false])
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>
