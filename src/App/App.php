<?php

namespace Moinframe\ParaDocs;

use Kirby\Toolkit\Str;
use Kirby\Cms\Page;
use Kirby\Cms\Pages;
use Kirby\Filesystem\Dir;
use Kirby\Filesystem\F;


class App
{
    public static function create()
    {
        // Create docs index page with plugins as children
        $indexPage = IndexPage::factory([
            'slug' => Options::slug(),
            'template' => 'paradocs-index',
            'content' => [
                'title' => Options::title(),
            ]
        ]);

        // Generate children for each plugin
        $app = new App();
        $children = [];

        $plugins = $indexPage->plugins();
        foreach ($plugins as $pluginName => $plugin) {
            $pluginPage = $app->createPluginDocs(
                $plugin,
                $indexPage
            );
            $children[$pluginName] = $pluginPage;
        }

        $indexPage->children = new Pages($children);
        return $indexPage;
    }

    /**
     * Generate documentation page hierarchy
     */
    private function createPluginDocs(array $plugin, Page $parent): ?Page
    {
        // Get all markdown files in the plugin's docs directory
        $docsPath = $plugin['root'] . '/' . ($plugin['config']['root'] ??  Options::slug());
        $fileStructure = $this->readFileStructureRecursively($docsPath);

        // Create root plugin documentation page
        $rootPage = $this->createPluginRootPage($plugin, $parent);

        // Generate children recursively
        $rootPage = $this->createChildPagesRecursively($rootPage, $fileStructure, $docsPath);

        return $rootPage;
    }

    /**
     * Get file structure from docs directory
     */
    private function readFileStructureRecursively(string $rootDir): array
    {
        $structure = [];

        if (!is_dir($rootDir)) {
            return $structure;
        }

        foreach (Dir::read($rootDir) as $item) {
            if ($item[0] === '.') continue; // Skip hidden files

            $path = $rootDir . '/' . $item;

            if (is_dir($path)) {
                $structure[$item] = [
                    'type' => 'directory',
                    'children' => $this->readFileStructureRecursively($path)
                ];
            } else if (pathinfo($path, PATHINFO_EXTENSION) === 'md') {
                $structure[$item] = [
                    'type' => 'file',
                    'root' => $path
                ];
            }
        }

        return $structure;
    }

    /**
     * Create plugin root page
     */
    private function createPluginRootPage(array $plugin, Page $parent): PluginPage
    {

        $parser = new MarkdownParser();
        $readmePath = $plugin['root'] . '/README.md';
        $parsed = ['content' => ''];

        // set readme content if it exists
        if (F::exists($readmePath)) {
            $parsed = $parser->parseFile($readmePath);
        }

        return PluginPage::factory([
            'slug' => $plugin['id'],
            'template' => 'paradocs-plugin',
            'parent' => $parent,
            'content' => [
                'title' => $parsed['meta']['title'] ?? $plugin['config']['title'] ?? $plugin['id'],
                'text' => $parsed['content'] ?? '',
                ...$plugin['info']
            ],
        ]);
    }

    /**
     * Generate child pages recursively
     * If it's a directory, create a page with all folders or markdown files below as children.
     * If a child is named index, use its content on the parent page.
     */
    private function createChildPagesRecursively(Page $parent, array $structure, string $basePath): Page
    {
        $children = [];
        $parser = new MarkdownParser();

        // First pass: Process index.md files for parent content
        if (isset($structure['index.md'])) {
            $indexItem = $structure['index.md'];
            $parsed = $parser->parseFile($indexItem['root']);

            // Overwrite Parent with content from index.md
            $parent = Page::factory([
                'slug' => $parent->slug(),
                'template' => $parent->template(),
                'parent' => $parent->parent(),
                'content' => [
                    ...$parent->content()->toArray(),
                    'title' => $parsed['meta']['title'] ?? $parent->title(),
                    'text' => $parsed['content'],
                ]
            ]);
        }

        // Second pass: Process all non-index files and directories
        foreach ($structure as $name => $item) {
            // Skip index.md as it's already processed
            if ($name === 'index.md') {
                continue;
            }

            if ($item['type'] === 'file') {
                // Create child page from markdown file
                $parsed = $parser->parseFile($item['root']);
                $slug = $parsed['slug'] ?? Str::slug(pathinfo($name, PATHINFO_FILENAME));

                $page = Page::factory([
                    'slug' => $slug,
                    'template' => 'paradocs-plugin-page',
                    'parent' => $parent,
                    'content' => [
                        'title' => $parsed['meta']['title'] ?? Str::ucfirst($slug),
                        'text' => $parsed['content'],
                        'description' => $parsed['meta']['description'] ?? '',
                    ]
                ]);

                $children[$slug] = $page;
            } else if ($item['type'] === 'directory') {
                // Create directory page
                $sectionSlug = Str::slug($name);

                $sectionPage = Page::factory([
                    'slug' => $sectionSlug,
                    'parent' => $parent,
                    'template' => 'paradocs-plugin-directory',
                    'content' => [
                        'title' => Str::ucfirst($name)
                    ]
                ]);

                // Process children recursively
                $sectionPage = $this->createChildPagesRecursively($sectionPage, $item['children'], $basePath . '/' . $name);

                // Always add directory pages to children array
                $children[$sectionSlug] = $sectionPage;
            }
        }

        // Set the children on the parent page
        $parent->children = new Pages($children);
        return $parent;
    }
}
