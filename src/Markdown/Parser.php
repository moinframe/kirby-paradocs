<?php

namespace Moinframe\ParaDocs\Markdown;

use Kirby\Toolkit\Str;
use Kirby\Data\Yaml;
use Kirby\Filesystem\F;
use Moinframe\ParaDocs\Options;

class Parser
{
	/**
	 * Collection of markdown processors
	 */
	protected ProcessorCollection $processors;

	/**
	 * Initialize the parser
	 */
	public function __construct()
	{
		$this->processors = new ProcessorCollection();
	}

	/**
	 * Get the processor collection
	 *
	 * @return ProcessorCollection
	 */
	public function processors(): ProcessorCollection
	{
		return $this->processors;
	}

	/**
	 * Add a processor
	 *
	 * @param Processor $processor The processor to add
	 * @return self
	 */
	public function addProcessor(Processor $processor): self
	{
		$this->processors->add($processor);
		return $this;
	}

	/**
	 * Remove a processor by name
	 *
	 * @param string $name The processor name to remove
	 * @return self
	 */
	public function removeProcessor(string $name): self
	{
		$this->processors->remove($name);
		return $this;
	}

	/**
	 * Parse a markdown file and convert to Kirby content
	 *
	 * @param string $path Path to the markdown file
	 * @return array{meta: array<string, mixed>, content: string, slug: string} Array with meta, content and slug
	 */
	public function parseFile(string $path): array
	{
		$kirby = kirby();
		$cache = $kirby->cache('moinframe.paradocs');
		$cacheEnabled = Options::cache();
		$cacheKey = 'md_' . md5($path) . '_' . filemtime($path);

		// Try to get from cache first
		if ($cacheEnabled && $cached = $cache->get($cacheKey)) {
			return $cached;
		}

		$content = F::read($path);
		if ($content === false) {
			throw new \Exception("Could not read file: {$path}");
		}
		$parsed = $this->extractStandardFrontmatter($content);
		$html = $this->parseMarkdown($parsed['content']);

		$result = [
			'meta' => $parsed['frontmatter'],
			'content' => $html,
			'slug' => $this->generateSlug($path)
		];

		// Save to cache
		if ($cacheEnabled) {
			$cache->set($cacheKey, $result, Options::cacheTime());
		}

		return $result;
	}

	/**
	 * Parse markdown with all registered processors
	 *
	 * @param string $content Markdown content
	 * @return string HTML content
	 */
	public function parseMarkdown(string $content): string
	{
		$placeholders = [];
		$processedContent = $content;

		// Apply all processors to the content (pre-markdown)
		foreach ($this->processors->all() as $name => $processor) {
			$result = $processor->process($processedContent);
			$processedContent = $result['content'];

			if (count($result['elements']) > 0) {
				$placeholders[$name] = $result['elements'];
			}
		}

		// Process markdown after all extractions
		$html = kirby()->markdown($processedContent);

		// Replace all placeholders with their rendered content
		foreach ($placeholders as $name => $elements) {
			foreach ($elements as $i => $element) {
				$placeholder = "<!-- {$name}_{$i} -->";
				$processor = $this->processors->get($name);
				if ($processor === null) {
					continue;
				}
				$rendered = $processor->render($element);
				$html = str_replace($placeholder, $rendered, $html);
			}
		}

		// Apply post-processing to HTML content
		foreach ($this->processors->all() as $processor) {
			if (method_exists($processor, 'postProcess')) {
				$html = $processor->postProcess($html);
			}
		}

		return $html;
	}

	/**
	 * Extract standard YAML frontmatter (--- delimited)
	 *
	 * @param string $content Raw markdown content
	 * @return array{frontmatter: array<string, mixed>, content: string} Frontmatter and content without frontmatter
	 */
	private function extractStandardFrontmatter(string $content): array
	{
		$frontmatter = [];
		$contentPart = $content;

		// Pattern to match standard frontmatter between --- delimiters
		if (preg_match('/^---\s*\n(.*?)\n---\s*\n(.*)/s', $content, $matches) === 1) {
			try {
				// Use Kirby's YAML parser on just the frontmatter section
				$frontmatter = Yaml::decode($matches[1]);
				$contentPart = $matches[2];
			} catch (\Exception) {
				// Silently fail and use original content if YAML parsing fails
			}
		}

		return [
			'frontmatter' => $frontmatter,
			'content' => $contentPart
		];
	}

	/**
	 * Generate slug from file path
	 *
	 * @param string $path File path
	 * @return string Slugified filename
	 */
	private function generateSlug(string $path): string
	{
		$filename = pathinfo($path, PATHINFO_FILENAME);
		return Str::slug($filename);
	}
}
