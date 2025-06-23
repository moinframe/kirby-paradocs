<?php

namespace Moinframe\ParaDocs\Markdown\Processors;

use Moinframe\ParaDocs\Markdown\Processor;

/**
 * Processor for removing relative images
 */
class RelativeImagesProcessor extends Processor
{
    /**
     * Create a new relative images processor
     */
    public function __construct()
    {
        parent::__construct('relativeImages');
    }

    /**
     * Process HTML content to remove relative image tags
     *
     * @param string $content Content to process
     * @return array{content: string, elements: array<mixed>} Processed content and extracted elements
     */
    public function process(string $content): array
    {
        // No elements to extract, just filter the content
        // This processor runs after markdown has been converted to HTML
        return [
            'content' => $content,
            'elements' => []
        ];
    }

    /**
     * Render HTML after removing relative image tags
     *
     * @param array<string, mixed> $data Extracted data (not used)
     * @return string HTML output
     */
    public function render(array $data): string
    {
        // Not used - this processor doesn't extract elements
        return '';
    }

    /**
     * Post-process HTML content to remove relative images
     *
     * @param string $html The HTML content to process
     * @return string Processed HTML content
     */
    public function postProcess(string $html): string
    {
        // Remove all image tags with relative src attributes
        $result = preg_replace(
            '/<img[^>]*src=["\']((?!https?:\/\/)[^"\']+)["\'][^>]*>/i',
            '',
            $html
        );
        
        return $result ?? $html;
    }
}
