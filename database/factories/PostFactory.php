<?php

namespace Database\Factories;

use App\Enums\PostStatus;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        $title = fake()->unique()->sentence(fake()->numberBetween(3, 6));

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'excerpt' => fake()->paragraph(),
            'body' => fake()->paragraphs(5, true),
            'status' => PostStatus::Published->value,
            'published_at' => now(),
        ];
    }

    public function draft(): self
    {
        return $this->state([
            'status' => PostStatus::Draft->value,
            'published_at' => null,
        ]);
    }

    public function scheduled(): self
    {
        return $this->state([
            'status' => PostStatus::Scheduled->value,
            'published_at' => now()->addDay(),
        ]);
    }

    public function withTags(int $count = 2): self
    {
        return $this->afterCreating(function (Post $post) use ($count) {
            $tags = Tag::factory()->count($count)->create();
            $post->tags()->sync($tags->pluck('id')->all());
        });
    }
}
