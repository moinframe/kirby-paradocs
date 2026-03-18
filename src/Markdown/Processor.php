<?php

namespace Moinframe\ParaDocs\Markdown;

/**
 * Base class for markdown processors
 */
abstract class Processor
{
    /**
     * Unique name for the processor
     */
    protected string $name;

    /**
     * Create a new processor
     *
     * @param string $name Unique name for the processor
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * Process markdown content
     * 
     * @param string $content Content to process
     * @return array{content: string, elements: array<mixed>} Processed content and extracted elements
     */
    abstract public function process(string $content): array;

    /**
     * Render extracted data to HTML
     * 
     * @param array<string, mixed> $data Extracted data
     * @return string HTML output
     */
    abstract public function render(array $data): string;

    /**
     * Post-process HTML content after markdown conversion
     *
     * @param string $html The HTML content to process
     * @return string Processed HTML content
     */
    public function postProcess(string $html): string
    {
        return $html;
    }

    /**
     * Get the processor name
     *
     * @return string Processor name
     */
    public function getName(): string
    {
        return $this->name;
    }
}