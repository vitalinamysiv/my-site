<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'username' => Str::lower((string) $this->input('username')),
            'email' => Str::lower((string) $this->input('email')),
            'age' => $this->input('age') === '' ? null : $this->input('age'),
        ]);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'username' => [
                'required',
                'string',
                'lowercase',
                'alpha_dash',
                'max:30',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'age' => ['nullable', 'integer', 'min:1', 'max:120'],
            'bio' => ['nullable', 'string', 'max:500'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'username.unique' => 'Этот логин уже занят.',
            'username.alpha_dash' => 'Логин может содержать буквы, цифры, дефис и подчёркивание.',
            'email.unique' => 'Эта почта уже зарегистрирована.',
            'email.email' => 'Почта должна быть адресом.',
            'age.integer' => 'Возраст должен быть числом.',
            'age.min' => 'Возраст должен быть от 1 до 120.',
            'age.max' => 'Возраст должен быть от 1 до 120.',
            'avatar.mimes' => 'Подойдут JPG, JPEG, PNG и WebP.',
        ];
    }
}
