<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-semibold">Подтверждение брони</h1>
    </x-slot>

    <div class="max-w-xl mx-auto px-4 sm:px-6 py-8">
        <div class="rounded-2xl border border-line bg-white p-6">
            <p>Забронировать «{{ $wishlist->title }}» из списка {{ $wishlist->user->name }}?</p>
            <p class="mt-2 text-sm text-muted">{{ $wishlist->priceLabel() }} · {{ $wishlist->category->name }}</p>
            <p class="mt-2 text-sm text-muted">После подтверждения подарок станет недоступен другим. {{ $wishlist->user->name }} не узнает, кто его забронировал.</p>

            <form method="POST" action="{{ route('reservations.store', $wishlist) }}" class="mt-6 flex gap-3">
                @csrf
                <x-primary-button>Забронировать</x-primary-button>
                <a href="{{ route('wishes.show', $wishlist) }}" class="rounded-full border border-line px-4 py-2 text-sm">Назад</a>
            </form>

            @if ($errors->has('wishlist'))
                <p class="mt-4 text-sm">{{ $errors->first('wishlist') }}</p>
            @endif
        </div>
    </div>
</x-app-layout>
