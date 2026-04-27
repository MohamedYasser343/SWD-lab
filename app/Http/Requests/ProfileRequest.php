<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim((string) $this->input('name')),
            'username' => trim((string) $this->input('username')) ?: null,
            'bio' => trim((string) $this->input('bio')) ?: null,
            'avatar' => trim((string) $this->input('avatar')) ?: null,
            'twitter' => trim((string) $this->input('twitter')) ?: null,
            'website' => trim((string) $this->input('website')) ?: null,
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'username' => [
                'nullable',
                'string',
                'max:255',
                'alpha_dash',
                Rule::unique('users', 'username')->ignore($this->user()->id),
            ],
            'bio' => ['nullable', 'string', 'max:1000'],
            'avatar' => ['nullable', 'string', 'max:2048'],
            'twitter' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'string', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'username.alpha_dash' => 'Username may contain letters, numbers, dashes, and underscores only.',
            'username.unique' => 'That username is already taken.',
        ];
    }
}
