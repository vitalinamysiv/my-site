<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-white border border-rose rounded-full font-medium text-sm text-ink hover:bg-petal focus:outline-none focus:ring-2 focus:ring-rose focus:ring-offset-2 transition']) }}>
    {{ $slot }}
</button>
