<?php

namespace App\Services;

use Illuminate\Support\Str;

class TableOfContentsExtractor
{
    /**
     * Parse H2/H3 tags out of the post body and return a nested tree:
     *   [ ['level'=>2, 'text'=>..., 'anchor'=>..., 'children'=>[ ['level'=>3,...] ]] ]
     *
     * Accepts HTML input. If the body is plain text or Markdown, returns an empty list.
     */
    public function extract(?string $body): array
    {
        $body = (string) $body;

        if ($body === '') {
            return [];
        }

        preg_match_all('/<h([23])[^>]*>(.*?)<\/h\1>/is', $body, $matches, PREG_SET_ORDER);

        $flat = [];
        foreach ($matches as $m) {
            $level = (int) $m[1];
            $text = trim(strip_tags($m[2]));

            if ($text === '') {
                continue;
            }

            $flat[] = [
                'level' => $level,
                'text' => $text,
                'anchor' => Str::slug($text),
            ];
        }

        return $this->nest($flat);
    }

    private function nest(array $flat): array
    {
        $tree = [];
        $currentTop = null;

        foreach ($flat as $node) {
            $node['children'] = [];

            if ($node['level'] === 2) {
                $tree[] = $node;
                $currentTop = array_key_last($tree);
                continue;
            }

            if ($currentTop === null) {
                $tree[] = $node;
                continue;
            }

            $tree[$currentTop]['children'][] = $node;
        }

        return $tree;
    }
}
