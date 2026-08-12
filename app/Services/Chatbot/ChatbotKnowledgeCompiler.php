<?php

declare(strict_types=1);

namespace App\Services\Chatbot;

use App\Models\ChatbotConfig;
use App\Models\LandingPage;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Compiles a LeadBot's knowledge blob from the tenant's OWN content — the
 * curated notes on the config, PLUS published landing pages and active
 * products — into one token-budgeted string for the system prompt.
 *
 * No vector DB (shared-hosting constraint): the blob is assembled in a fixed
 * priority order (curated notes → pages → products) and hard-truncated to a
 * character budget, so a large catalogue degrades to deterministic truncation
 * rather than an unbounded prompt / bill. Always scoped explicitly by the
 * config's tenant_id so it is correct from the public (unauthenticated)
 * chatbot context.
 */
class ChatbotKnowledgeCompiler
{
    /** Total character budget for the compiled blob (≈1.5k tokens). */
    public const BUDGET_CHARS = 6_000;

    /** Assembly caps so one tenant's content can't balloon the build. */
    private const MAX_PAGES = 10;
    private const MAX_PRODUCTS = 40;

    public function compile(ChatbotConfig $config): string
    {
        $tenantId = (int) $config->tenant_id;
        $sections = [];

        // 1. Curated notes on the config win first — the tenant's own words.
        $curated = trim((string) ($config->knowledge ?? ''));
        if ($curated !== '') {
            $sections[] = $curated;
        }

        // 2. Published landing pages (title + meta + visible section text).
        $pageBlocks = [];
        foreach ($this->publishedPages($tenantId) as $page) {
            $text = $this->pageText($page);
            if ($text !== '') {
                $pageBlocks[] = $text;
            }
        }
        if ($pageBlocks !== []) {
            $sections[] = "Website pages:\n" . implode("\n\n", $pageBlocks);
        }

        // 3. Active products / services.
        $productLines = $this->productLines($tenantId);
        if ($productLines !== []) {
            $sections[] = "Products & services:\n" . implode("\n", $productLines);
        }

        return mb_substr(trim(implode("\n\n", $sections)), 0, self::BUDGET_CHARS);
    }

    /** @return Collection<int, LandingPage> */
    private function publishedPages(int $tenantId): Collection
    {
        return LandingPage::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantId)
            ->where('status', 'published')
            ->with('sections')
            ->latest('updated_at')
            ->limit(self::MAX_PAGES)
            ->get();
    }

    private function pageText(LandingPage $page): string
    {
        $lines = [];

        $heading = trim((string) ($page->title ?: $page->name));
        if ($heading !== '') {
            $lines[] = $heading;
        }
        $meta = trim((string) ($page->meta_description ?? ''));
        if ($meta !== '') {
            $lines[] = $meta;
        }

        foreach ($page->sections as $section) {
            if ($section->is_visible === false) {
                continue;
            }
            foreach ($this->stringLeaves((array) $section->content) as $text) {
                $lines[] = $text;
            }
        }

        return implode("\n", array_values(array_unique(array_filter($lines))));
    }

    /**
     * Human-readable string leaves of a section's content array, skipping
     * non-prose tokens (colors, URLs/paths, very short values).
     *
     * @param  array<mixed>  $content
     * @return list<string>
     */
    private function stringLeaves(array $content): array
    {
        $out = [];
        array_walk_recursive($content, static function ($value) use (&$out): void {
            if (! is_string($value)) {
                return;
            }
            $value = trim($value);
            if (mb_strlen($value) < 3) {
                return;
            }
            if (Str::startsWith($value, ['#', 'http://', 'https://', 'data:', '/'])) {
                return;
            }
            $out[] = $value;
        });

        return $out;
    }

    /** @return list<string> */
    private function productLines(int $tenantId): array
    {
        return Product::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->orderBy('name')
            ->limit(self::MAX_PRODUCTS)
            ->get()
            ->map(function (Product $product): string {
                $line = '- ' . trim((string) $product->name);

                if ($product->price !== null && (string) $product->price !== '') {
                    $line .= ' (' . trim((string) $product->currency) . ' ' . $product->price . ')';
                }
                $description = trim((string) ($product->description ?? ''));
                if ($description !== '') {
                    $line .= ': ' . $description;
                }

                return $line;
            })
            ->all();
    }
}
