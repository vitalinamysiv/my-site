<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-semibold">Друзья</h1>
    </x-slot>

    <div class="max-w-3xl mx-auto space-y-8 px-4 py-8 sm:px-6">
        <form method="GET" action="{{ route('friends.index') }}" class="flex gap-2">
            <input name="q" value="{{ $query }}" placeholder="Имя или логин" class="w-full rounded-xl border-line">
            <button class="shrink-0 rounded-full bg-blush px-4 py-2 text-sm font-medium">Найти</button>
        </form>

        @if ($errors->has('friend'))
            <p class="text-sm">{{ $errors->first('friend') }}</p>
        @endif

        @if ($query !== '')
            <section class="space-y-3">
                <h2 class="text-lg font-semibold">Поиск</h2>
                @forelse ($people as $person)
                    @include('friends.person', ['person' => $person])
                @empty
                    <p class="rounded-2xl border border-dashed border-line bg-white p-6 text-center text-muted">Никого с таким именем нет.</p>
                @endforelse
            </section>
        @endif

        <section class="space-y-3">
            <h2 class="text-lg font-semibold">Подписки</h2>
            @forelse ($following as $person)
                @include('friends.person', ['person' => $person])
            @empty
                <p class="rounded-2xl border border-dashed border-line bg-white p-6 text-center text-muted">Вы ещё ни на кого не подписаны.</p>
            @endforelse
        </section>

        <section class="space-y-3">
            <h2 class="text-lg font-semibold">Подписчики</h2>
            @forelse ($followers as $person)
                @include('friends.person', ['person' => $person])
            @empty
                <p class="rounded-2xl border border-dashed border-line bg-white p-6 text-center text-muted">На вас пока никто не подписан.</p>
            @endforelse
        </section>
    </div>
</x-app-layout>
