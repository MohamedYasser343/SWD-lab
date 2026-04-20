<?php

namespace App\Http\Requests;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $title = trim((string) $this->input('title'));
        $slug = trim((string) $this->input('slug'));
        $excerpt = trim((string) $this->input('excerpt'));
        $body = trim((string) $this->input('body'));
        $categoryName = trim((string) $this->input('category'));

        $categoryId = null;
        if ($categoryName !== '') {
            $categorySlug = Str::slug($categoryName);
            if ($categorySlug !== '') {
                $categoryId = Category::firstOrCreate(
                    ['slug' => $categorySlug],
                    ['name' => $categoryName],
                )->id;
            }
        }

        $this->merge([
            'title' => $title,
            'slug' => Str::slug($slug !== '' ? $slug : $title),
            'excerpt' => $excerpt !== '' ? $excerpt : null,
            'body' => $body,
            'category_id' => $categoryId,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('posts', 'slug')->ignore($this->route('post')),
            ],
            'category_id' => ['nullable', 'integer', Rule::exists('categories', 'id')],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'body' => ['required', 'string', 'min:20'],
        ];
    }

    /**
     * Custom error messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Please give this post a title.',
            'slug.unique' => 'That slug is already taken — try a different one or clear the field.',
            'body.required' => 'The body is empty — share what you want to say.',
            'body.min' => 'The body is a little short (at least 20 characters).',
            'excerpt.max' => 'Keep the excerpt under 500 characters.',
        ];
    }
}
