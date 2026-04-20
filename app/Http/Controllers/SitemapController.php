<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $urls = collect();

        $urls->push([
            'loc' => route('posts.index'),
            'lastmod' => now()->toAtomString(),
            'changefreq' => 'daily',
            'priority' => '1.0',
        ]);

        foreach (Post::query()->published()->latest('published_at')->get() as $post) {
            $urls->push([
                'loc' => route('posts.show', $post),
                'lastmod' => optional($post->updated_at ?? $post->published_at)->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ]);
        }

        foreach (Tag::query()->get() as $tag) {
            $urls->push([
                'loc' => route('tags.show', $tag),
                'lastmod' => optional($tag->updated_at)->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.5',
            ]);
        }

        foreach (User::query()->whereNotNull('username')->get() as $author) {
            $urls->push([
                'loc' => route('authors.show', $author),
                'lastmod' => optional($author->updated_at)->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.4',
            ]);
        }

        $xmlUrls = $urls->map(function (array $url) {
            return sprintf(
                "<url>\n<loc>%s</loc>\n<lastmod>%s</lastmod>\n<changefreq>%s</changefreq>\n<priority>%s</priority>\n</url>",
                e($url['loc']),
                e((string) $url['lastmod']),
                $url['changefreq'],
                $url['priority'],
            );
        })->implode("\n");

        $xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
{$xmlUrls}
</urlset>
XML;

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
        ]);
    }
}
