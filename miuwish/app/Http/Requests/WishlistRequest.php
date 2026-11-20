<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class WishlistRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        foreach (['description', 'url'] as $field) {
            if ($this->input($field) === '') {
                $this->merge([$field => null]);
            }
        }
    }

    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'price' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'url' => ['nullable', 'url:http,https', 'max:2048'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Укажите название.',
            'price.required' => 'Укажите цену.',
            'price.numeric' => 'Цена должна быть числом.',
            'price.min' => 'Цена не может быть меньше нуля.',
            'price.max' => 'Цена слишком большая.',
            'url.url' => 'Ссылка должна быть адресом страницы.',
            'category_id.required' => 'Выберите категорию.',
            'category_id.exists' => 'Такой категории нет.',
            'image.image' => 'Нужен файл изображения.',
            'image.mimes' => 'Подойдут JPG, JPEG, PNG и WebP.',
            'image.max' => 'Изображение больше 4 МБ.',
        ];
    }
}
