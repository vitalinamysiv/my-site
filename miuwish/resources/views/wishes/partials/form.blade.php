@csrf
@if ($wishlist->exists && $wishlist->image)
    <img src="{{ Storage::url($wishlist->image) }}" alt="{{ $wishlist->title }}" class="mb-4 block h-auto max-h-80 w-auto max-w-full rounded-xl">
    <p class="-mt-2 mb-4 text-sm text-muted">Новый файл заменит это изображение.</p>
@endif
@if ($wishlist->exists)
    @method('PUT')
@endif

<div class="space-y-4">
    <label class="block text-sm">
        <span class="text-muted">Название</span>
        <input name="title" value="{{ old('title', $wishlist->title) }}" required class="mt-1 w-full rounded-xl border-line">
        <x-input-error class="mt-2" :messages="$errors->get('title')" />
    </label>

    <label class="block text-sm">
        <span class="text-muted">Категория</span>
        <select name="category_id" required class="mt-1 w-full rounded-xl border-line">
            <option value="">Выберите</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected((string) old('category_id', $wishlist->category_id) === (string) $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('category_id')" />
    </label>

    <label class="block text-sm">
        <span class="text-muted">Цена, ₽</span>
        <input type="number" min="0" step="0.01" name="price" value="{{ old('price', $wishlist->price) }}" required class="mt-1 w-full rounded-xl border-line">
        <x-input-error class="mt-2" :messages="$errors->get('price')" />
    </label>

    <label class="block text-sm">
        <span class="text-muted">Описание</span>
        <textarea name="description" rows="4" class="mt-1 w-full rounded-xl border-line">{{ old('description', $wishlist->description) }}</textarea>
        <x-input-error class="mt-2" :messages="$errors->get('description')" />
    </label>

    <label class="block text-sm">
        <span class="text-muted">Ссылка</span>
        <input type="url" name="url" value="{{ old('url', $wishlist->url) }}" placeholder="https://" class="mt-1 w-full rounded-xl border-line">
        <x-input-error class="mt-2" :messages="$errors->get('url')" />
    </label>

    <label class="block text-sm">
        <span class="text-muted">Изображение, JPG, JPEG, PNG или WebP</span>
        <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" class="mt-1 block w-full text-sm">
        <x-input-error class="mt-2" :messages="$errors->get('image')" />
    </label>
</div>

<div class="mt-6">
    <x-primary-button>{{ $wishlist->exists ? 'Сохранить' : 'Добавить' }}</x-primary-button>
</div>
