<article class="flex items-center justify-between gap-3 rounded-2xl border border-line bg-white p-4">
    <a href="{{ route('profiles.show', $person) }}" class="flex min-w-0 items-center gap-3">
        @if ($person->avatar)
            <img src="{{ Storage::url($person->avatar) }}" alt="" class="h-12 w-12 shrink-0 rounded-full object-cover">
        @else
            <div class="h-12 w-12 shrink-0 rounded-full bg-petal"></div>
        @endif
        <span class="min-w-0">
            <span class="block truncate font-semibold">{{ $person->name }}</span>
            <span class="block text-sm text-muted">{{ '@'.$person->username }}</span>
        </span>
    </a>
    @if ((int) Auth::id() === (int) $person->id)
        <span class="shrink-0 text-sm text-muted">Это вы</span>
    @elseif ($followingIds->contains(fn ($id) => (int) $id === (int) $person->id))
        <form method="POST" action="{{ route('friends.destroy', $person) }}">
            @csrf
            @method('DELETE')
            <button class="shrink-0 rounded-full border border-line px-4 py-2 text-sm">Отписаться</button>
        </form>
    @else
        <form method="POST" action="{{ route('friends.store', $person) }}">
            @csrf
            <button class="shrink-0 rounded-full bg-blush px-4 py-2 text-sm font-medium">Подписаться</button>
        </form>
    @endif
</article>
