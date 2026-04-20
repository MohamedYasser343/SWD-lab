<?php

namespace App\Models;

use App\Enums\PostStatus;
use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

#[Fillable([
    'user_id',
    'title',
    'slug',
    'category_id',
    'excerpt',
    'body',
    'status',
    'published_at',
    'featured_image',
    'meta_title',
    'meta_description',
    'og_image',
    'reading_minutes',
])]
class Post extends Model
{
    /** @use HasFactory<PostFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'status' => PostStatus::class,
            'published_at' => 'datetime',
            'views_count' => 'integer',
            'likes_count' => 'integer',
            'reading_minutes' => 'integer',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->latest();
    }

    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }

    public function views(): HasMany
    {
        return $this->hasMany(PostView::class);
    }

    public function isLikedBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $this->likes()->where('user_id', $user->id)->exists();
    }

    public function isBookmarkedBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $this->bookmarks()->where('user_id', $user->id)->exists();
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', PostStatus::Published->value)
            ->where(function (Builder $q) {
                $q->whereNull('published_at')->orWhere('published_at', '<=', now());
            });
    }

    public function scopeScheduled(Builder $query): Builder
    {
        return $query->where('status', PostStatus::Scheduled->value);
    }

    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', PostStatus::Draft->value);
    }

    public function scopeForFeed(Builder $query): Builder
    {
        return $query->published()->orderByDesc('published_at')->limit(20);
    }

    protected function readingTime(): Attribute
    {
        return Attribute::get(function (): int {
            if ($this->reading_minutes) {
                return (int) $this->reading_minutes;
            }

            $words = str_word_count(strip_tags((string) $this->body));

            return max(1, (int) ceil($words / 200));
        });
    }

    protected function ogImageUrl(): Attribute
    {
        return Attribute::get(function (): ?string {
            return $this->og_image ?: $this->featured_image ?: null;
        });
    }

    protected function metaTitleOrDefault(): Attribute
    {
        return Attribute::get(fn (): string => $this->meta_title ?: (string) $this->title);
    }

    protected function metaDescriptionOrDefault(): Attribute
    {
        return Attribute::get(fn (): string => (string) ($this->meta_description ?: $this->excerpt ?: Str::limit(strip_tags((string) $this->body), 160)));
    }
}
