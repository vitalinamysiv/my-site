<a href="{{ route('wishes.show', $wishlist) }}" class="block h-fit overflow-hidden rounded-2xl border border-line bg-white">
    @if ($wishlist->image)
        <img src="{{ Storage::url($wishlist->image) }}" alt="{{ $wishlist->title }}" class="block h-auto w-full">
    @else
        <div class="h-44 bg-petal"></div>
    @endif
    <div class="p-4">
        <div class="flex items-start justify-between gap-3">
            <h2 class="font-semibold">{{ $wishlist->title }}</h2>
            @if ($showStatus ?? true)
                <span class="shrink-0 rounded-full px-2 py-1 text-xs {{ $wishlist->isReserved() ? 'bg-rose text-ink' : 'bg-petal text-ink' }}">
                    {{ $wishlist->statusLabel() }}
                </span>
            @endif
        </div>
        <p class="mt-2 text-sm">{{ $wishlist->priceLabel() }}</p>
        <p class="text-sm text-muted">{{ $wishlist->category->name }}</p>
    </div>
</a>
