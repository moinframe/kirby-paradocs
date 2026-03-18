<?php

namespace Moinframe\ParaDocs;

use Kirby\Toolkit\Str;
use Kirby\Filesystem\Dir;
use Kirby\Filesystem\F;
use Moinframe\ParaDocs\Options;
use Moinframe\ParaDocs\Page\IndexPage;
use Moinframe\ParaDocs\Markdown\Parser;
use Moinframe\ParaDocs\Markdown\Processors\RelativeLinksProcessor;

class App
{
    /**
     * Create the full index page with all plugin children
     */
    public static function create(): IndexPage
    {
        return IndexPage::factory(self::getTreeProps());
    }

    /**
     * Create an index page containing only the plugin subtree needed for a given path.
     */
    public static function createForPath(string $path): ?IndexPage
    {
        $props = self::getTreeProps();
        $segments = explode('/', $path);
        $pluginSlug = $segments[0] ?? null;

        if ($pluginSlug === null) {
            return null;
        }

        // Find the matching plugin in cached props
        $pluginProps = null;
        foreach ($props['children'] as $child) {
            if ($child['slug'] === $pluginSlug) {
                $pluginProps = $child;
                break;
            }
        }

        if ($pluginProps === null) {
            return null;
        }

        // Create index with only the needed plugin as child
        $props['children'] = [$pluginProps];

        return IndexPage::factory($props);
    }

    /**
     * Get the full page tree as a props array, cached.
     * @return array<string, mixed>
     */
    private static function getTreeProps(): array
    {
        $kirby = kirby();
        $cache = $kirby->cache('moinframe.paradocs');
        $cacheEnabled = Options::cache();
        $cacheKey = 'page_tree_props';

        $props = null;
        if ($cacheEnabled) {
            $props = $cache->get($cacheKey);
        }

        if ($props === null) {
            $app = new App();
            $childrenProps = [];

            $plugins = Plugins::getAll();
            foreach ($plugins as $plugin) {
                $childrenProps[] = $app->buildPluginPageProps($plugin);
            }

            $props = [
                'slug' => Options::slug(),
                'template' => 'paradocs-index',
                'content' => [
                    'title' => Options::title(),
                ],
                'children' => $childrenProps
            ];

            if ($cacheEnabled) {
                $cache->set($cacheKey, $props, Options::cacheTime());
            }
        }

        return $props;
    }

    /**
     * Build page props for a plugin's documentation
     * @param array<string, mixed> $plugin
     * @return array<string, mixed>
     */
    private function buildPluginPageProps(array $plugin): array
    {
        // Get all markdown files in the plugin's docs directory
        $docsPath = $plugin['root'] . '/' . ($plugin['config']['root'] ?? 'docs');
        $fileStructure = $this->readFileStructureRecursively($docsPath);

        // Build root plugin page props
        $pluginSlug = $plugin['id'];
        $rootProps = $this->buildPluginRootProps($plugin);

        // Build children props recursively and merge into root
        $childrenResult = $this->buildChildrenPropsRecursively($fileStructure, $docsPath, $pluginSlug);

        // If index.md provided content overrides, merge them
        if ($childrenResult['parentOverrides'] !== null) {
            $rootProps['content'] = array_merge($rootProps['content'], $childrenResult['parentOverrides']);
        }

        $rootProps['children'] = $childrenResult['children'];

        return $rootProps;
    }

    /**
     * Get file structure from docs directory
     * @return array<string, mixed>
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
     * Create a parser
     *
     * @return Parser
     */
    private function createParser(): Parser
    {
        $parser = new Parser();

        // Trigger hook to allow custom processors
        kirby()->trigger('paradocs.parser.ready', ['parser' => $parser]);

        return $parser;
    }

    /**
     * Build props for a plugin root page
     * @param array<string, mixed> $plugin
     * @return array<string, mixed>
     */
    private function buildPluginRootProps(array $plugin): array
    {
        // Get parser with hooks applied
        $parser = $this->createParser();

        $readmePath = $plugin['root'] . '/README.md';
        $parsed = ['content' => ''];

        // Get logo URL (stored as string for cacheability)
        $logo = null;

        if ($plugin['config'] !== null && isset($plugin['config']['logo'])) {
            $asset = $plugin['plugin']->asset($plugin['config']['logo']);
            $logo = $asset?->url();
        }
        // set readme content if it exists
        if (F::exists($readmePath)) {
            $docsRoot = $plugin['config']['root'] ?? 'docs';
            $this->setLinksContext($parser, '', $plugin['id'], $docsRoot);
            $parsed = $parser->parseFile($readmePath);
        }

        return [
            'slug' => $plugin['id'],
            'template' => 'paradocs-plugin',
            'model' => 'paradocs-plugin',
            'content' => [
                'title' => $parsed['meta']['title'] ?? $plugin['config']['title'] ?? $plugin['id'],
                'description' => $parsed['meta']['description'] ?? $plugin['config']['description'] ?? $plugin['info']['description'] ?? '',
                'text' => $parsed['content'],
                'logo' => $logo,
                ...$plugin['info']
            ],
        ];
    }

    /**
     * Build children page props recursively
     * If it's a directory, create props with all folders or markdown files below as children.
     * If a child is named index, use its content to override the parent page.
     * @param array<string, mixed> $structure
     * @return array{children: array<string, mixed>, parentOverrides: array<string, mixed>|null}
     */
    private function buildChildrenPropsRecursively(array $structure, string $basePath, string $pluginSlug, string $relDir = ''): array
    {
        $childrenProps = [];
        $parentOverrides = null;

        // Get parser with hooks applied
        $parser = $this->createParser();

        // First pass: Process index.md files for parent content overrides
        if (isset($structure['index.md'])) {
            $indexItem = $structure['index.md'];
            $indexRelPath = $relDir !== '' ? $relDir . '/index.md' : 'index.md';
            $this->setLinksContext($parser, $indexRelPath, $pluginSlug);
            $parsed = $parser->parseFile($indexItem['root']);

            $parentOverrides = [
                'title' => $parsed['meta']['title'] ?? null,
                'description' => $parsed['meta']['description'] ?? '',
                'text' => $parsed['content'],
            ];

            // Remove null title so it doesn't override existing title
            if ($parentOverrides['title'] === null) {
                unset($parentOverrides['title']);
            }
        }

        // Second pass: Process all non-index files and directories
        foreach ($structure as $name => $item) {
            // Skip index.md as it's already processed
            if ($name === 'index.md') {
                continue;
            }

            if ($item['type'] === 'file') {
                // Create child page props from markdown file
                $fileRelPath = $relDir !== '' ? $relDir . '/' . $name : $name;
                $this->setLinksContext($parser, $fileRelPath, $pluginSlug);
                $parsed = $parser->parseFile($item['root']);
                $slug = $parsed['slug'];

                $childrenProps[] = [
                    'slug' => $slug,
                    'template' => 'paradocs-plugin-page',
                    'content' => [
                        'title' => $parsed['meta']['title'] ?? Str::ucfirst($slug),
                        'text' => $parsed['content'],
                        'description' => $parsed['meta']['description'] ?? '',
                    ]
                ];
            } else if ($item['type'] === 'directory') {
                // Build directory page props
                $sectionSlug = Str::slug(preg_replace('/^\d+-/', '', $name));

                $sectionProps = [
                    'slug' => $sectionSlug,
                    'template' => 'paradocs-plugin-directory',
                    'content' => [
                        'title' => Str::ucfirst(preg_replace('/^\d+-/', '', $name)),
                        'description' => ''
                    ]
                ];

                // Process children recursively
                $childRelDir = $relDir !== '' ? $relDir . '/' . $name : $name;
                $childrenResult = $this->buildChildrenPropsRecursively($item['children'], $basePath . '/' . $name, $pluginSlug, $childRelDir);

                // Apply index.md overrides to directory page
                if ($childrenResult['parentOverrides'] !== null) {
                    $sectionProps['content'] = array_merge($sectionProps['content'], $childrenResult['parentOverrides']);
                }

                $sectionProps['children'] = $childrenResult['children'];
                $childrenProps[] = $sectionProps;
            }
        }

        return [
            'children' => $childrenProps,
            'parentOverrides' => $parentOverrides,
        ];
    }

    /**
     * Set context on the RelativeLinksProcessor for the current file
     */
    private function setLinksContext(Parser $parser, string $fileRelPath, string $pluginSlug, string $docsRoot = ''): void
    {
        $processor = $parser->processors()->get('relativeLinks');
        if ($processor instanceof RelativeLinksProcessor) {
            $baseUrl = '/' . Options::slug() . '/' . $pluginSlug;
            $processor->setContext($fileRelPath, $baseUrl, $docsRoot);
        }
    }
}
