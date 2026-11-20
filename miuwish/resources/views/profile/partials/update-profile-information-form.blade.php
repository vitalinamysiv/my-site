<section>
    <header>
        <h2 class="text-lg font-medium text-ink">
            Профиль
        </h2>

        <p class="mt-1 text-sm text-muted">
            Имя, логин, возраст, коротко о себе и фотография.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" value="Имя" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="username" value="Логин" />
            <x-text-input id="username" name="username" type="text" class="mt-1 block w-full" :value="old('username', $user->username)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('username')" />
        </div>

        <div>
            <x-input-label for="age" value="Возраст" />
            <x-text-input id="age" name="age" type="number" class="mt-1 block w-full" :value="old('age', $user->age)" min="1" max="120" />
            <x-input-error class="mt-2" :messages="$errors->get('age')" />
        </div>

        <div>
            <x-input-label for="bio" value="О себе" />
            <textarea id="bio" name="bio" rows="3" class="mt-1 block w-full rounded-md border-gray-300">{{ old('bio', $user->bio) }}</textarea>
            <x-input-error class="mt-2" :messages="$errors->get('bio')" />
        </div>

        <div>
            <x-input-label for="avatar" value="Фото" />
            <img
                data-avatar-preview
                src="{{ $user->avatar ? Storage::url($user->avatar) : '' }}"
                alt="{{ $user->name }}"
                class="mt-2 h-16 w-16 rounded-full object-cover {{ $user->avatar ? '' : 'hidden' }}"
            >
            <div class="mt-3 flex flex-wrap items-center gap-3">
                <input id="avatar" name="avatar" data-avatar-input type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" class="sr-only">
                <label for="avatar" class="inline-flex cursor-pointer items-center rounded-full border border-line bg-white px-4 py-2 text-sm font-medium text-ink">Выбрать фото</label>
                <span data-avatar-chosen class="text-sm text-muted"></span>
            </div>
            <p class="mt-2 text-sm text-muted">Загрузите фото и выберите квадратную область. Сохранится аватар 512×512.</p>
            <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
        </div>

        <div data-avatar-crop class="fixed inset-0 z-50 hidden items-center justify-center bg-ink/40 p-4">
            <div class="w-full max-w-lg rounded-2xl bg-white p-4">
                <p class="font-medium">Область аватарки</p>
                <p class="mt-1 text-sm text-muted">Перетащите фото и рамку. Колёсико меняет масштаб. В профиль попадёт выбранный квадрат.</p>
                <div class="mt-3 h-80 overflow-hidden rounded-xl bg-petal">
                    <img data-avatar-crop-image alt="" class="block max-h-80">
                </div>
                <div class="mt-4 flex justify-end gap-2">
                    <button type="button" data-avatar-crop-cancel class="rounded-full border border-line px-4 py-2 text-sm">Отмена</button>
                    <button type="button" data-avatar-crop-apply class="rounded-full bg-blush px-4 py-2 text-sm font-medium">Готово</button>
                </div>
            </div>
        </div>

        <div>
            <x-input-label for="email" value="Почта" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
