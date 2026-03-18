<?php

namespace Moinframe\ParaDocs\Markdown\Processors;

use Moinframe\ParaDocs\Markdown\Processor;

/**
 * Processor for transforming relative markdown links to documentation URLs
 */
class RelativeLinksProcessor extends Processor
{
    protected string $currentFileRelPath = '';
    protected string $baseUrl = '';
    protected string $docsRoot = '';

    /**
     * Create a new relative links processor
     */
    public function __construct()
    {
        parent::__construct('relativeLinks');
    }

    /**
     * Set the context for resolving relative links
     *
     * @param string $currentFileRelPath File path relative to docs root (e.g. "02-write-docs/01-markdown.md")
     * @param string $baseUrl Base URL for the plugin (e.g. "/docs/plugin-slug")
     * @param string $docsRoot The docs folder name (e.g. "docs") to strip from resolved paths for files outside the docs root
     */
    public function setContext(string $currentFileRelPath, string $baseUrl, string $docsRoot = ''): void
    {
        $this->currentFileRelPath = $currentFileRelPath;
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->docsRoot = $docsRoot;
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
     * Post-process HTML content to transform relative links
     *
     * @param string $html The HTML content to process
     * @return string Processed HTML content
     */
    public function postProcess(string $html): string
    {
        if ($this->baseUrl === '') {
            return $html;
        }

        $result = preg_replace_callback(
            '/<a\s([^>]*?)href=["\']([^"\']+)["\']/i',
            function (array $matches): string {
                $href = $matches[2];

                // Skip absolute URLs, fragments, and schemes like mailto:
                if (preg_match('#^(https?://|//|mailto:|tel:|\#)#', $href) === 1) {
                    return $matches[0];
                }

                // Split off fragment
                $fragment = '';
                $hashPos = strpos($href, '#');
                if ($hashPos !== false) {
                    $fragment = substr($href, $hashPos);
                    $href = substr($href, 0, $hashPos);
                }

                // If only a fragment was present, skip
                if ($href === '') {
                    return $matches[0];
                }

                $resolved = $this->resolveRelativePath($href);
                $url = $this->baseUrl . '/' . $resolved;

                // Clean up double/trailing slashes
                $url = rtrim($url, '/');

                return '<a ' . $matches[1] . 'href="' . $url . $fragment . '"';
            },
            $html
        );

        return $result ?? $html;
    }

    /**
     * Resolve a relative path against the current file's location
     *
     * @param string $href The relative href to resolve
     * @return string Resolved and cleaned path
     */
    private function resolveRelativePath(string $href): string
    {
        // Get the directory of the current file
        $currentDir = $this->currentFileRelPath !== ''
            ? dirname($this->currentFileRelPath)
            : '.';

        if ($currentDir === '.') {
            $currentDir = '';
        }

        // Combine current directory with the href
        $combined = $currentDir !== ''
            ? $currentDir . '/' . $href
            : $href;

        // Normalize the path (resolve ../ and ./)
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

        // Strip docs root prefix if present (for links from README.md referencing the docs folder)
        if ($this->docsRoot !== '' && count($resolved) > 0 && $resolved[0] === $this->docsRoot) {
            array_shift($resolved);
        }

        // Strip .md extension from the last segment
        if (count($resolved) > 0) {
            $last = array_key_last($resolved);
            $resolved[$last] = preg_replace('/\.md$/i', '', $resolved[$last]) ?? $resolved[$last];
        }

        // Strip numeric prefixes from each segment
        foreach ($resolved as $i => $segment) {
            $resolved[$i] = preg_replace('/^\d+-/', '', $segment) ?? $segment;
        }

        // Remove trailing "index"
        if (count($resolved) > 0 && $resolved[array_key_last($resolved)] === 'index') {
            array_pop($resolved);
        }

        return implode('/', $resolved);
    }
}
