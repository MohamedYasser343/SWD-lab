<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class FeedController extends Controller
{
    public function __invoke(): Response
    {
        $posts = Post::query()
            ->with(['user', 'category'])
            ->forFeed()
            ->get();

        $siteUrl = url('/');
        $feedUrl = route('feed');
        $updated = optional($posts->first()?->updated_at ?? now())->toRssString();

        $items = $posts->map(function (Post $post) {
            $description = $post->meta_description_or_default ?: Str::limit(strip_tags($post->body), 240);

            return sprintf(
                "<item>\n<title><![CDATA[%s]]></title>\n<link>%s</link>\n<guid isPermaLink=\"true\">%s</guid>\n<description><![CDATA[%s]]></description>\n<author>%s</author>\n<pubDate>%s</pubDate>\n</item>",
                $post->title,
                route('posts.show', $post),
                route('posts.show', $post),
                $description,
                e($post->user?->email ?? 'hello@example.com') . ' (' . e($post->user?->name ?? 'Author') . ')',
                optional($post->published_at ?? $post->created_at)->toRssString(),
            );
        })->implode("\n");

        $xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
<channel>
<title>The Lagoon</title>
<link>{$siteUrl}</link>
<description>A quiet blog about craft, rooms, and the things we keep nearby.</description>
<language>en</language>
<lastBuildDate>{$updated}</lastBuildDate>
<atom:link href="{$feedUrl}" rel="self" type="application/rss+xml" />
{$items}
</channel>
</rss>
XML;

        return response($xml, 200, [
            'Content-Type' => 'application/rss+xml; charset=UTF-8',
        ]);
    }
}
