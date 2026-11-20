<x-app-layout>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-10">
        <div class="grid gap-8 lg:grid-cols-[1.1fr_0.9fr] items-center">
            <div>
                <p class="text-sm text-muted">Списки желаний</p>
                <h1 class="mt-2 text-4xl font-semibold tracking-tight">MiuWish</h1>
                <p class="mt-4 text-lg text-muted max-w-xl">
                    Соберите подарки в одном списке и поделитесь им. Друзья видят, какое желание занято. Вы не видите ни это, ни имя того, кто его готовит.
                </p>
                <div class="mt-6 flex flex-wrap gap-3">
                    @auth
                        <a href="{{ route('wishes.index') }}" class="rounded-full bg-blush px-5 py-2.5 font-medium">Мои желания</a>
                    @else
                        <a href="{{ route('register') }}" class="rounded-full bg-blush px-5 py-2.5 font-medium">Создать список</a>
                        <a href="{{ route('login') }}" class="rounded-full border border-line bg-white px-5 py-2.5 font-medium">Войти</a>
                    @endauth
                </div>
            </div>
            <img src="{{ asset('images/logo.jpg') }}" alt="Кот MiuWish с подарком" class="w-full rounded-3xl border border-line">
        </div>

        <div class="mt-12 grid gap-4 sm:grid-cols-3">
            <div class="rounded-2xl border border-line bg-white p-5">
                <h2 class="font-semibold">Свой список</h2>
                <p class="mt-2 text-sm text-muted">Название, цена, категория, описание, ссылка и картинка. Всё добавляется вручную.</p>
            </div>
            <div class="rounded-2xl border border-line bg-white p-5">
                <h2 class="font-semibold">Тихая бронь</h2>
                <p class="mt-2 text-sm text-muted">Один подарок бронирует один человек. На странице владельца нет метки, занято желание или нет.</p>
            </div>
            <div class="rounded-2xl border border-line bg-white p-5">
                <h2 class="font-semibold">Категории и фильтры</h2>
                <p class="mt-2 text-sm text-muted">Одежда, техника, книги и остальные разделы. Можно сузить список по цене и отсортировать его.</p>
            </div>
        </div>

        @if (app()->environment('local'))
            <div class="mt-10 rounded-2xl border border-line bg-petal p-5">
                <h2 class="font-semibold">Учебные входы</h2>
                <p class="mt-2 text-sm">Пароль у всех: password</p>
                <p class="mt-2 text-sm">anna, misha, lina, kostya — или почта вида anna@miuwish.test</p>
            </div>
        @endif
    </div>
</x-app-layout>
