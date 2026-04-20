<?php

namespace Tests\Unit;

use App\Services\TableOfContentsExtractor;
use PHPUnit\Framework\TestCase;

class TableOfContentsExtractorTest extends TestCase
{
    private TableOfContentsExtractor $extractor;

    protected function setUp(): void
    {
        $this->extractor = new TableOfContentsExtractor;
    }

    public function test_returns_empty_for_plain_text(): void
    {
        $this->assertSame([], $this->extractor->extract('Just a paragraph with no headings.'));
    }

    public function test_extracts_h2_and_h3_headings(): void
    {
        $body = <<<'HTML'
            <h2>Beginnings</h2>
            <p>Some text</p>
            <h3>First light</h3>
            <h2>Middle</h2>
            HTML;

        $toc = $this->extractor->extract($body);

        $this->assertCount(2, $toc);
        $this->assertSame('Beginnings', $toc[0]['text']);
        $this->assertSame('beginnings', $toc[0]['anchor']);
        $this->assertCount(1, $toc[0]['children']);
        $this->assertSame('First light', $toc[0]['children'][0]['text']);
        $this->assertSame('Middle', $toc[1]['text']);
        $this->assertCount(0, $toc[1]['children']);
    }
}
