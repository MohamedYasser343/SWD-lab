<?php

namespace App\Http\Requests;

use App\Enums\PostStatus;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $title = trim((string) $this->input('title'));
        $slug = trim((string) $this->input('slug'));
        $excerpt = trim((string) $this->input('excerpt'));
        $body = trim((string) $this->input('body'));
        $categoryName = trim((string) $this->input('category'));
        $status = trim((string) $this->input('status')) ?: PostStatus::Draft->value;
        $publishedAt = trim((string) $this->input('published_at')) ?: null;

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

        $tagIds = $this->resolveTagIds((string) $this->input('tags'));

        $this->merge([
            'title' => $title,
            'slug' => Str::slug($slug !== '' ? $slug : $title),
            'excerpt' => $excerpt !== '' ? $excerpt : null,
            'body' => $body,
            'category_id' => $categoryId,
            'status' => $status,
            'published_at' => $publishedAt,
            'tag_ids' => $tagIds,
            'meta_title' => trim((string) $this->input('meta_title')) ?: null,
            'meta_description' => trim((string) $this->input('meta_description')) ?: null,
            'og_image' => trim((string) $this->input('og_image')) ?: null,
        ]);
    }

    private function resolveTagIds(string $raw): array
    {
        if ($raw === '') {
            return [];
        }

        $names = array_filter(array_map('trim', explode(',', $raw)));

        return collect($names)
            ->map(function (string $name) {
                $slug = Str::slug($name);

                if ($slug === '') {
                    return null;
                }

                return Tag::firstOrCreate(
                    ['slug' => $slug],
                    ['name' => $name],
                )->id;
            })
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

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
            'status' => ['required', Rule::in(array_column(PostStatus::cases(), 'value'))],
            'published_at' => ['nullable', 'date'],
            'tag_ids' => ['array'],
            'tag_ids.*' => ['integer', Rule::exists('tags', 'id')],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'og_image' => ['nullable', 'string', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Please give this post a title.',
            'slug.unique' => 'That slug is already taken — try a different one or clear the field.',
            'body.required' => 'The body is empty — share what you want to say.',
            'body.min' => 'The body is a little short (at least 20 characters).',
            'excerpt.max' => 'Keep the excerpt under 500 characters.',
            'status.in' => 'Pick a valid status: draft, published, scheduled, or archived.',
        ];
    }

    public function postAttributes(): array
    {
        return Arr::except($this->validated(), ['tag_ids']);
    }

    public function tagIds(): array
    {
        return (array) $this->validated()['tag_ids'] ?? [];
    }
}
