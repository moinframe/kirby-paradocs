<?php

namespace Moinframe\ParaDocs\Markdown\Processors;

use Moinframe\ParaDocs\Markdown\Processor;

/**
 * Processor for adding anchor links to headings
 */
class HeadingAnchorProcessor extends Processor
{
    /**
     * Track used IDs to handle duplicates
     * @var array<string, int>
     */
    protected array $usedIds = [];

    /**
     * Create a new heading anchor processor
     */
    public function __construct()
    {
        parent::__construct('headingAnchors');
    }

    /**
     * @param string $content Content to process
     * @return array{content: string, elements: array<mixed>}
     */
    public function process(string $content): array
    {
        return [
            'content' => $content,
            'elements' => []
        ];
    }

    /**
     * @param array<string, mixed> $data Extracted data (not used)
     * @return string HTML output
     */
    public function render(array $data): string
    {
        return '';
    }

    /**
     * Post-process HTML to add IDs and anchor links to h2–h6 elements
     *
     * @param string $html The HTML content to process
     * @return string Processed HTML content
     */
    public function postProcess(string $html): string
    {
        $this->usedIds = [];

        $result = preg_replace_callback(
            '/<(h[2-6])(?:\s[^>]*)?>(.+?)<\/\1>/i',
            function (array $matches): string {
                $tag = $matches[1];
                $innerHtml = $matches[2];

                $textContent = strip_tags($innerHtml);
                $id = $this->generateSlug($textContent);
                $id = $this->ensureUniqueId($id);

                $escapedText = htmlspecialchars($textContent, ENT_QUOTES, 'UTF-8');

                return '<' . $tag . ' id="' . $id . '">'
                    . $innerHtml
                    . '<a href="#' . $id . '" class="heading-anchor" aria-label="Link to section \'' . $escapedText . '\'">#</a>'
                    . '</' . $tag . '>';
            },
            $html
        );

        return $result ?? $html;
    }

    /**
     * Generate a URL-safe slug from text
     */
    protected function generateSlug(string $text): string
    {
        $slug = strtolower($text);
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug) ?? $slug;
        $slug = preg_replace('/[\s-]+/', '-', $slug) ?? $slug;
        $slug = trim($slug, '-');

        return $slug !== '' ? $slug : 'section';
    }

    /**
     * Ensure the ID is unique by appending a suffix if needed
     */
    protected function ensureUniqueId(string $id): string
    {
        if (!isset($this->usedIds[$id])) {
            $this->usedIds[$id] = 0;
            return $id;
        }

        $this->usedIds[$id]++;
        $uniqueId = $id . '-' . $this->usedIds[$id];

        while (isset($this->usedIds[$uniqueId])) {
            $this->usedIds[$id]++;
            $uniqueId = $id . '-' . $this->usedIds[$id];
        }

        $this->usedIds[$uniqueId] = 0;
        return $uniqueId;
    }
}
