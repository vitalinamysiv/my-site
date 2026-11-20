import Cropper from 'cropperjs';
import 'cropperjs/dist/cropper.css';

const SIZE = 512;

function ready(fn) {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', fn);
    } else {
        fn();
    }
}

ready(() => {
    const input = document.querySelector('[data-avatar-input]');
    const dialog = document.querySelector('[data-avatar-crop]');
    const image = document.querySelector('[data-avatar-crop-image]');
    const preview = document.querySelector('[data-avatar-preview]');
    if (!input || !dialog || !image) {
        return;
    }

    let cropper = null;
    let sourceUrl = null;

    const close = () => {
        cropper?.destroy();
        cropper = null;
        if (sourceUrl) {
            URL.revokeObjectURL(sourceUrl);
            sourceUrl = null;
        }
        image.removeAttribute('src');
        dialog.classList.add('hidden');
        dialog.classList.remove('flex');
    };

    input.addEventListener('change', () => {
        const file = input.files?.[0];
        input.value = '';
        if (!file || !file.type.startsWith('image/')) {
            return;
        }

        if (sourceUrl) {
            URL.revokeObjectURL(sourceUrl);
        }
        sourceUrl = URL.createObjectURL(file);
        dialog.classList.remove('hidden');
        dialog.classList.add('flex');
        cropper?.destroy();
        image.onload = () => {
            cropper = new Cropper(image, {
                aspectRatio: 1,
                viewMode: 1,
                autoCropArea: 1,
                dragMode: 'move',
                background: false,
                responsive: true,
            });
        };
        image.src = sourceUrl;
    });

    dialog.querySelector('[data-avatar-crop-cancel]')?.addEventListener('click', close);

    dialog.querySelector('[data-avatar-crop-apply]')?.addEventListener('click', () => {
        if (!cropper) {
            return;
        }

        cropper.getCroppedCanvas({
            width: SIZE,
            height: SIZE,
            imageSmoothingQuality: 'high',
        }).toBlob((blob) => {
            if (!blob) {
                return;
            }

            const file = new File([blob], 'avatar.jpg', { type: 'image/jpeg' });
            const transfer = new DataTransfer();
            transfer.items.add(file);
            input.files = transfer.files;

            if (preview) {
                preview.src = URL.createObjectURL(blob);
                preview.classList.remove('hidden');
            }
            const chosen = document.querySelector('[data-avatar-chosen]');
            if (chosen) {
                chosen.textContent = 'Фото выбрано';
            }

            close();
        }, 'image/jpeg', 0.92);
    });
});
