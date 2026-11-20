<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2 bg-white border border-line rounded-full font-medium text-sm text-ink hover:bg-petal focus:outline-none focus:ring-2 focus:ring-blush focus:ring-offset-2 transition']) }}>
    {{ $slot }}
</button>
