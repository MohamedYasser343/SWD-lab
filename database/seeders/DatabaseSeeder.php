<?php

namespace Database\Seeders;

use App\Enums\PostStatus;
use App\Enums\UserRole;
use App\Models\Bookmark;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Like;
use App\Models\NewsletterSubscriber;
use App\Models\Post;
use App\Models\PostView;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $admin = User::factory()->admin()->create([
            'name' => 'Amira Admin',
            'username' => 'amira',
            'email' => 'admin@example.com',
            'bio' => 'Editor-in-chief at the Cozy Lagoon. Probably refilling the kettle.',
        ]);

        $primaryAuthor = User::factory()->author()->create([
            'name' => 'Omar Author',
            'username' => 'omar',
            'email' => 'author@example.com',
            'bio' => 'Writes about distributed systems and slow mornings.',
        ]);

        $primaryReader = User::factory()->create([
            'name' => 'Reader User',
            'username' => 'reader',
            'email' => 'reader@example.com',
            'role' => UserRole::Reader->value,
        ]);

        $extraAuthors = User::factory()->author()->count(4)->create();
        $extraReaders = User::factory()->count(20)->create();

        $authors = collect([$admin, $primaryAuthor])->concat($extraAuthors);
        $readers = collect([$primaryReader])->concat($extraReaders);
        $allUsers = $authors->concat($readers);

        $categories = collect([
            ['name' => 'Tutorials', 'description' => 'Step-by-step guides and walkthroughs.'],
            ['name' => 'Engineering', 'description' => 'Deep dives into how things are built.'],
            ['name' => 'Design', 'description' => 'Notes on craft, type, and color.'],
            ['name' => 'Product', 'description' => 'Strategy, roadmaps, and the work behind the work.'],
            ['name' => 'News', 'description' => 'What is happening in the lagoon and beyond.'],
            ['name' => 'Opinion', 'description' => 'Hot takes, served lukewarm.'],
        ])->map(fn (array $row) => Category::create([
            ...$row,
            'slug' => Str::slug($row['name']),
        ]));

        $tags = Tag::factory()->count(20)->create();

        $publishedPosts = Post::factory()
            ->count(30)
            ->make()
            ->each(function (Post $post) use ($authors, $categories, $tags) {
                $post->user_id = $authors->random()->id;
                $post->category_id = $categories->random()->id;
                $post->published_at = Carbon::now()->subDays(rand(1, 180))->subHours(rand(0, 23));
                $post->save();
                $post->tags()->sync($tags->random(rand(2, 4))->pluck('id')->all());
            });

        $draftPosts = Post::factory()->draft()->count(5)->make()
            ->each(function (Post $post) use ($authors, $categories, $tags) {
                $post->user_id = $authors->random()->id;
                $post->category_id = $categories->random()->id;
                $post->save();
                $post->tags()->sync($tags->random(rand(1, 3))->pluck('id')->all());
            });

        $scheduledPosts = Post::factory()->scheduled()->count(3)->make()
            ->each(function (Post $post) use ($authors, $categories, $tags) {
                $post->user_id = $authors->random()->id;
                $post->category_id = $categories->random()->id;
                $post->published_at = Carbon::now()->addDays(rand(1, 14));
                $post->save();
                $post->tags()->sync($tags->random(rand(1, 3))->pluck('id')->all());
            });

        foreach ($publishedPosts as $post) {
            $this->seedComments($post, $allUsers);
            $this->seedLikes($post, $allUsers);
            $this->seedBookmarks($post, $readers);
            $this->seedViews($post, $allUsers);
        }

        $this->seedNewsletter();
    }

    private function seedComments(Post $post, $allUsers): void
    {
        $topLevelCount = rand(0, 5);
        if ($topLevelCount === 0) {
            return;
        }

        $createdAt = $post->published_at ?? Carbon::now()->subDays(7);

        $topLevel = collect();
        for ($i = 0; $i < $topLevelCount; $i++) {
            $topLevel->push(Comment::create([
                'post_id' => $post->id,
                'user_id' => $allUsers->random()->id,
                'parent_id' => null,
                'body' => fake()->paragraph(rand(1, 3)),
                'created_at' => $createdAt->copy()->addHours(rand(1, 72)),
                'updated_at' => $createdAt->copy()->addHours(rand(1, 72)),
            ]));
        }

        foreach ($topLevel->random(min($topLevel->count(), rand(0, 3))) as $parent) {
            $replyCount = rand(1, 2);
            for ($i = 0; $i < $replyCount; $i++) {
                $reply = Comment::create([
                    'post_id' => $post->id,
                    'user_id' => $allUsers->random()->id,
                    'parent_id' => $parent->id,
                    'body' => fake()->sentence(rand(8, 20)),
                    'created_at' => $parent->created_at->copy()->addHours(rand(1, 24)),
                    'updated_at' => $parent->created_at->copy()->addHours(rand(1, 24)),
                ]);

                if (rand(0, 100) < 30) {
                    Comment::create([
                        'post_id' => $post->id,
                        'user_id' => $allUsers->random()->id,
                        'parent_id' => $reply->id,
                        'body' => fake()->sentence(rand(6, 14)),
                        'created_at' => $reply->created_at->copy()->addHours(rand(1, 12)),
                        'updated_at' => $reply->created_at->copy()->addHours(rand(1, 12)),
                    ]);
                }
            }
        }
    }

    private function seedLikes(Post $post, $allUsers): void
    {
        $count = rand(0, 15);
        if ($count === 0) {
            return;
        }

        $likers = $allUsers->random(min($count, $allUsers->count()));
        foreach ($likers as $user) {
            Like::create([
                'user_id' => $user->id,
                'likeable_type' => Post::class,
                'likeable_id' => $post->id,
            ]);
        }

        $post->update(['likes_count' => $likers->count()]);
    }

    private function seedBookmarks(Post $post, $readers): void
    {
        $count = rand(0, 8);
        if ($count === 0) {
            return;
        }

        $bookmarkers = $readers->random(min($count, $readers->count()));
        foreach ($bookmarkers as $user) {
            Bookmark::create([
                'user_id' => $user->id,
                'post_id' => $post->id,
            ]);
        }
    }

    private function seedViews(Post $post, $allUsers): void
    {
        $count = rand(5, 30);
        $base = $post->published_at ?? Carbon::now()->subDays(30);

        for ($i = 0; $i < $count; $i++) {
            $viewedAt = $base->copy()->addHours(rand(0, 24 * 60));
            $user = rand(0, 100) < 40 ? $allUsers->random() : null;
            $sessionId = Str::random(32);

            PostView::create([
                'post_id' => $post->id,
                'user_id' => $user?->id,
                'session_id' => $sessionId,
                'day_key' => $viewedAt->format('Ymd'),
                'ip_hash' => hash('sha256', fake()->ipv4()),
                'viewed_at' => $viewedAt,
            ]);
        }

        $post->update(['views_count' => $count]);
    }

    private function seedNewsletter(): void
    {
        for ($i = 0; $i < 18; $i++) {
            NewsletterSubscriber::create([
                'email' => fake()->unique()->safeEmail(),
                'confirm_token' => null,
                'unsubscribe_token' => Str::random(40),
                'confirmed_at' => Carbon::now()->subDays(rand(1, 90)),
                'unsubscribed_at' => null,
            ]);
        }

        for ($i = 0; $i < 4; $i++) {
            NewsletterSubscriber::create([
                'email' => fake()->unique()->safeEmail(),
                'confirm_token' => Str::random(40),
                'unsubscribe_token' => null,
                'confirmed_at' => null,
                'unsubscribed_at' => null,
            ]);
        }

        for ($i = 0; $i < 3; $i++) {
            $confirmedAt = Carbon::now()->subDays(rand(60, 180));
            NewsletterSubscriber::create([
                'email' => fake()->unique()->safeEmail(),
                'confirm_token' => null,
                'unsubscribe_token' => Str::random(40),
                'confirmed_at' => $confirmedAt,
                'unsubscribed_at' => $confirmedAt->copy()->addDays(rand(1, 50)),
            ]);
        }
    }
}
