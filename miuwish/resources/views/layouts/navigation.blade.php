<nav class="bg-white border-b border-line">
    <div class="max-w-5xl mx-auto px-4 sm:px-6">
        <div class="flex justify-between h-16 items-center">
            <a href="{{ route('home') }}" class="flex items-center gap-2 font-semibold text-ink">
                <img src="{{ asset('images/logo.jpg') }}" alt="" class="h-10 w-10 rounded-full object-cover">
                MiuWish
            </a>

            <div class="hidden sm:flex items-center gap-6 text-sm">
                @auth
                    <a href="{{ route('friends.index') }}" class="text-muted hover:text-ink">Друзья</a>
                    <a href="{{ route('wishes.index') }}" class="text-muted hover:text-ink">Мои желания</a>
                    <a href="{{ route('reservations.index') }}" class="text-muted hover:text-ink">Мои бронирования</a>
                    <a href="{{ route('profile.edit') }}" class="text-muted hover:text-ink">{{ Auth::user()->name }}</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-muted hover:text-ink">Выйти</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-muted hover:text-ink">Войти</a>
                    <a href="{{ route('register') }}" class="rounded-full bg-blush px-4 py-2 text-ink">Регистрация</a>
                @endauth
            </div>

            <details class="sm:hidden relative">
                <summary class="list-none cursor-pointer text-sm text-muted">Меню</summary>
                <div class="absolute right-0 mt-2 w-52 rounded-xl border border-line bg-white p-3 space-y-2 text-sm">
                    @auth
                        <a class="block" href="{{ route('friends.index') }}">Друзья</a>
                        <a class="block" href="{{ route('wishes.index') }}">Мои желания</a>
                        <a class="block" href="{{ route('reservations.index') }}">Мои бронирования</a>
                        <a class="block" href="{{ route('profile.edit') }}">Профиль</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit">Выйти</button>
                        </form>
                    @else
                        <a class="block" href="{{ route('login') }}">Войти</a>
                        <a class="block" href="{{ route('register') }}">Регистрация</a>
                    @endauth
                </div>
            </details>
        </div>
    </div>
</nav>
