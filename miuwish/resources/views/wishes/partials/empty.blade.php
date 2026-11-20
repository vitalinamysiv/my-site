<p class="rounded-2xl border border-dashed border-line bg-white p-8 text-center text-muted">
    @if (request()->filled('category') || request()->filled('min_price') || request()->filled('max_price'))
        @if (is_numeric(request('min_price')) && is_numeric(request('max_price')) && (float) request('min_price') > (float) request('max_price'))
            Цена «от» больше цены «до».
        @else
            В этом отборе ничего нет.
        @endif
    @else
        {{ $message }}
    @endif
</p>
