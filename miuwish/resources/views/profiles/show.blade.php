<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-2xl font-semibold">{{ $profile->name }}</h1>
            <p class="text-sm text-muted">{{ '@'.$profile->username }}</p>
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-8">
        <section class="mb-8 flex gap-4 rounded-2xl border border-line bg-white p-5">
            @if ($profile->avatar)
                <img src="{{ Storage::url($profile->avatar) }}" alt="" class="h-20 w-20 rounded-full object-cover">
            @else
                <div class="h-20 w-20 rounded-full bg-petal"></div>
            @endif
            <div>
                @if ($profile->ageLabel())
                    <p class="text-sm text-muted">{{ $profile->ageLabel() }}</p>
                @endif
                @if ($profile->bio)
                    <p class="mt-1">{{ $profile->bio }}</p>
                @endif
                <div class="mt-3 flex flex-wrap items-center gap-2">
                    <x-copy-link :url="route('profiles.show', $profile)" />
                    @auth
                        @if ((int) Auth::id() !== (int) $profile->id)
                            @if ($followed)
                                <form method="POST" action="{{ route('friends.destroy', $profile) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="rounded-full border border-line px-4 py-2 text-sm">Отписаться</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('friends.store', $profile) }}">
                                    @csrf
                                    <button class="rounded-full bg-blush px-4 py-2 text-sm font-medium">Подписаться</button>
                                </form>
                            @endif
                        @endif
                    @endauth
                </div>
                @if ($errors->has('friend'))
                    <p class="mt-2 text-sm">{{ $errors->first('friend') }}</p>
                @endif
            </div>
        </section>

        <h2 class="mb-4 text-lg font-semibold">Желания</h2>
        @include('wishes.partials.filters')

        @if ($wishlists->isEmpty())
            @include('wishes.partials.empty', ['message' => 'Список пока пуст.'])
        @else
            <div class="grid items-start gap-4 sm:grid-cols-2">
                @foreach ($wishlists as $wishlist)
                    @include('wishes.partials.card', ['wishlist' => $wishlist, 'showStatus' => (int) Auth::id() !== (int) $profile->id])
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>
