<?php

namespace Moinframe\ParaDocs\Markdown\Processors;

use Moinframe\ParaDocs\Markdown\Processor;
use Moinframe\ParaDocs\Options;

/**
 * Processor for rewriting relative image paths to served URLs
 */
class RelativeImagesProcessor extends Processor
{
    protected string $currentFileRelPath = '';
    protected string $pluginSlug = '';
    protected string $docsRoot = '';

    /**
     * Create a new relative images processor
     */
    public function __construct()
    {
        parent::__construct('relativeImages');
    }

    /**
     * Set the context for resolving relative image paths
     *
     * @param string $currentFileRelPath File path relative to docs root (e.g. "02-write-docs/01-markdown.md")
     * @param string $pluginSlug The plugin slug (e.g. "moinframe-paradocs")
     * @param string $docsRoot The docs folder name (e.g. "docs") to strip from resolved paths
     */
    public function setContext(string $currentFileRelPath, string $pluginSlug, string $docsRoot = ''): void
    {
        $this->currentFileRelPath = $currentFileRelPath;
        $this->pluginSlug = $pluginSlug;
        $this->docsRoot = $docsRoot;
    }

    /**
     * Process HTML content
     *
     * @param string $content Content to process
     * @return array{content: string, elements: array<mixed>} Processed content and extracted elements
     */
    public function process(string $content): array
    {
        return [
            'content' => $content,
            'elements' => []
        ];
    }

    /**
     * Render HTML
     *
     * @param array<string, mixed> $data Extracted data (not used)
     * @return string HTML output
     */
    public function render(array $data): string
    {
        return '';
    }

    /**
     * Post-process HTML content to rewrite relative image paths
     *
     * @param string $html The HTML content to process
     * @return string Processed HTML content
     */
    public function postProcess(string $html): string
    {
        if ($this->pluginSlug === '') {
            return $html;
        }

        $result = preg_replace_callback(
            '/<img([^>]*?)src=["\']((?!https?:\/\/)[^"\']+)["\']([^>]*)>/i',
            function (array $matches): string {
                $src = $matches[2];

                // Skip data URIs
                if (str_starts_with($src, 'data:')) {
                    return $matches[0];
                }

                $resolved = $this->resolveRelativePath($src);
                $url = '/' . Options::slug() . '/media/' . $this->pluginSlug . '/' . $resolved;

                return '<img' . $matches[1] . 'src="' . $url . '"' . $matches[3] . '>';
            },
            $html
        );

        return $result ?? $html;
    }

    /**
     * Resolve a relative path against the current file's location
     *
     * @param string $src The relative src to resolve
     * @return string Resolved and cleaned path
     */
    private function resolveRelativePath(string $src): string
    {
        $currentDir = $this->currentFileRelPath !== ''
            ? dirname($this->currentFileRelPath)
            : '.';

        if ($currentDir === '.') {
            $currentDir = '';
        }

        $combined = $currentDir !== ''
            ? $currentDir . '/' . $src
            : $src;

        $parts = explode('/', $combined);
        $resolved = [];

        foreach ($parts as $part) {
            if ($part === '.' || $part === '') {
                continue;
            }
            if ($part === '..') {
                array_pop($resolved);
            } else {
                $resolved[] = $part;
            }
        }

        // Strip docs root prefix if present (for README.md referencing docs/images/...)
        if ($this->docsRoot !== '' && count($resolved) > 0 && $resolved[0] === $this->docsRoot) {
            array_shift($resolved);
        }

        return implode('/', $resolved);
    }
}
