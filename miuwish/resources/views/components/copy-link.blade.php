@props(['url'])

<button
    type="button"
    data-url="{{ $url }}"
    onclick="navigator.clipboard.writeText(this.dataset.url).then(() => { this.textContent = 'Ссылка скопирована'; })"
    class="rounded-full border border-line bg-white px-4 py-2 text-sm"
>Скопировать ссылку</button>
