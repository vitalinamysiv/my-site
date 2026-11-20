<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-semibold">Мои бронирования</h1>
    </x-slot>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 py-8 space-y-4">
        @forelse ($reservations as $reservation)
            <article class="rounded-2xl border border-line bg-white p-4 sm:flex sm:items-center sm:justify-between gap-4">
                <div>
                    <a href="{{ route('wishes.show', $reservation->wishlist) }}" class="font-semibold">{{ $reservation->wishlist->title }}</a>
                    <p class="text-sm text-muted">Список: {{ $reservation->wishlist->user->name }} · {{ $reservation->wishlist->priceLabel() }}</p>
                    <p class="text-sm text-muted">{{ $reservation->wishlist->category->name }}</p>
                </div>
                <form method="POST" action="{{ route('reservations.destroy', $reservation) }}" onsubmit="return confirm('Снять бронь? Желание снова смогут забронировать другие.')">
                    @csrf
                    @method('DELETE')
                    <button class="mt-3 sm:mt-0 rounded-full border border-line px-4 py-2 text-sm">Отменить бронь</button>
                </form>
            </article>
        @empty
            <p class="rounded-2xl border border-dashed border-line bg-white p-8 text-center text-muted">Вы ещё ничего не забронировали.</p>
        @endforelse
    </div>
</x-app-layout>
