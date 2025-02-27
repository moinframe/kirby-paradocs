<?php

namespace Moinframe\ParaDocs;

use Kirby\Toolkit\Str;
use Kirby\Data\Yaml;
use Kirby\Filesystem\F;

class MarkdownParser
{

    /**
     * Parse a markdown file and convert to Kirby content
     */
    public function parseFile(string $path): array
    {
        $content = F::read($path);

        // Extract standard YAML frontmatter
        $parsed = $this->extractStandardFrontmatter($content);

        // Convert markdown to HTML using Kirby's markdown parser
        $html = kirby()->markdown($parsed['content']);

        // Remove all relative images
        $html = preg_replace('/<img[^>]*src=["\']((?!https?:\/\/)[^"\']+)["\'][^>]*>/i', '', $html);

        return [
            'meta' => $parsed['frontmatter'],
            'content' => $html,
            'slug' => $this->generateSlug($path)
        ];
    }
    /**
     * Extract standard YAML frontmatter (--- delimited)
     */
    private function extractStandardFrontmatter(string $content): array
    {
        $frontmatter = [];
        $contentPart = $content;

        // Pattern to match standard frontmatter between --- delimiters
        if (preg_match('/^---\s*\n(.*?)\n---\s*\n(.*)/s', $content, $matches)) {
            try {
                // Use Kirby's YAML parser on just the frontmatter section
                $frontmatter = Yaml::decode($matches[1]);
                $contentPart = $matches[2];
            } catch (\Exception $e) {
            }
        }

        return [
            'frontmatter' => $frontmatter,
            'content' => $contentPart
        ];
    }

    /**
     * Generate slug from file path
     */
    private function generateSlug(string $path): string
    {
        $filename = pathinfo($path, PATHINFO_FILENAME);
        return Str::slug($filename);
    }
}
