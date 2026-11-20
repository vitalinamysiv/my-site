<x-app-layout>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 py-8">
        <a href="{{ route('profiles.show', $wishlist->user) }}" class="text-sm text-muted">Все желания · {{ $wishlist->user->name }}</a>

        <div class="mt-4 overflow-hidden rounded-2xl border border-line bg-white">
            @if ($wishlist->image)
                <img src="{{ Storage::url($wishlist->image) }}" alt="{{ $wishlist->title }}" class="block h-auto w-full">
            @else
                <div class="h-40 bg-petal"></div>
            @endif
            <div class="p-6">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <h1 class="text-2xl font-semibold">{{ $wishlist->title }}</h1>
                    @unless ((int) Auth::id() === (int) $wishlist->user_id)
                        <span class="rounded-full px-3 py-1 text-sm {{ $wishlist->isReserved() ? 'bg-rose' : 'bg-petal' }}">{{ $wishlist->statusLabel() }}</span>
                    @endunless
                </div>
                <p class="mt-3 text-lg">{{ $wishlist->priceLabel() }}</p>
                <p class="text-sm text-muted">{{ $wishlist->category->name }}</p>
                @if ($wishlist->description)
                    <p class="mt-4 whitespace-pre-line">{{ $wishlist->description }}</p>
                @endif
                @if ($wishlist->url)
                    <a href="{{ $wishlist->url }}" class="mt-4 inline-block text-sm underline" target="_blank" rel="noopener noreferrer">Ссылка на товар</a>
                @endif

                @if ($errors->has('wishlist'))
                    <p class="mt-4 text-sm text-ink">{{ $errors->first('wishlist') }}</p>
                @endif

                <div class="mt-6 flex flex-wrap items-center gap-3">
                    @auth
                        @if ((int) Auth::id() === (int) $wishlist->user_id)
                            <a href="{{ route('wishes.edit', $wishlist) }}" class="rounded-full border border-line px-4 py-2 text-sm">Изменить</a>
                            <form method="POST" action="{{ route('wishes.destroy', $wishlist) }}" onsubmit="return confirm('Удалить желание?')">
                                @csrf
                                @method('DELETE')
                                <button class="rounded-full border border-line px-4 py-2 text-sm">Удалить</button>
                            </form>
                        @elseif ($myReservationId)
                            <p class="w-full text-sm">Вы забронировали это желание. Владелец не видит ваше имя.</p>
                            <form method="POST" action="{{ route('reservations.destroy', $myReservationId) }}" onsubmit="return confirm('Снять бронь? Желание снова смогут забронировать другие.')">
                                @csrf
                                @method('DELETE')
                                <button class="rounded-full border border-line px-4 py-2 text-sm">Снять бронь</button>
                            </form>
                        @elseif (! $wishlist->isReserved())
                            <a href="{{ route('reservations.create', $wishlist) }}" class="rounded-full bg-blush px-4 py-2 text-sm font-medium">Забронировать</a>
                        @else
                            <p class="text-sm">Это желание уже забронировано.</p>
                        @endif
                    @else
                        @if (! $wishlist->isReserved())
                            <a href="{{ route('reservations.create', $wishlist) }}" class="rounded-full bg-blush px-4 py-2 text-sm font-medium">Войти, чтобы забронировать</a>
                        @else
                            <p class="text-sm">Это желание уже забронировано.</p>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
