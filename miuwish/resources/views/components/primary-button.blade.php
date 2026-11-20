<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-blush rounded-full font-medium text-sm text-ink hover:bg-rose focus:outline-none focus:ring-2 focus:ring-blush focus:ring-offset-2 transition']) }}>
    {{ $slot }}
</button>
