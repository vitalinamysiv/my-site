@php
    $action = $action ?? url()->current();
@endphp

<form method="GET" action="{{ $action }}" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4 mb-6">
    <label class="text-sm">
        <span class="text-muted">Категория</span>
        <select name="category" class="mt-1 w-full rounded-xl border-line">
            <option value="">Все</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected((string) request('category') === (string) $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
    </label>
    <label class="text-sm">
        <span class="text-muted">Цена от</span>
        <input type="number" min="0" step="1" name="min_price" value="{{ request('min_price') }}" class="mt-1 w-full rounded-xl border-line">
    </label>
    <label class="text-sm">
        <span class="text-muted">Цена до</span>
        <input type="number" min="0" step="1" name="max_price" value="{{ request('max_price') }}" class="mt-1 w-full rounded-xl border-line">
    </label>
    <label class="text-sm">
        <span class="text-muted">Сортировка</span>
        <select name="sort" class="mt-1 w-full rounded-xl border-line">
            @foreach ([
                'created_at|desc' => 'Сначала новые',
                'created_at|asc' => 'Сначала старые',
                'price|asc' => 'Сначала дешевле',
                'price|desc' => 'Сначала дороже',
                'title|asc' => 'По названию А–Я',
                'title|desc' => 'По названию Я–А',
            ] as $value => $label)
                <option value="{{ $value }}" @selected(request('sort', 'created_at|desc') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </label>
    <div class="sm:col-span-2 lg:col-span-4 flex flex-wrap gap-3">
        <button type="submit" class="rounded-full bg-blush px-4 py-2 text-sm font-medium">Показать</button>
        @if (request()->hasAny(['category', 'min_price', 'max_price', 'sort']))
            <a href="{{ $action }}" class="rounded-full border border-line bg-white px-4 py-2 text-sm">Сбросить</a>
        @endif
    </div>
</form>
