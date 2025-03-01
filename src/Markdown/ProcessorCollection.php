<?php

namespace Moinframe\ParaDocs\Markdown;

use Moinframe\ParaDocs\Highlighter\Interface\Highlighter;
use Moinframe\ParaDocs\Markdown\Processors\AlertProcessor;
use Moinframe\ParaDocs\Markdown\Processors\CodeBlockProcessor;
use Moinframe\ParaDocs\Markdown\Processors\RelativeImagesProcessor;

/**
 * Collection of markdown processors
 */
class ProcessorCollection
{
    /**
     * Registered processors
     */
    protected array $processors = [];

    /**
     * Create a new processor collection
     */
    public function __construct()
    {
        $this->registerDefaultProcessors();
    }

    /**
     * Register the default processors
     */
    protected function registerDefaultProcessors(): void
    {
        // Create highlighter based on configuration
        $highlighter = $this->createHighlighter();
        
        // Register code block processor with highlighter
        $this->add(new CodeBlockProcessor($highlighter));
        
        // Register alert processor
        $this->add(new AlertProcessor());
        
        // Register relative images processor
        $this->add(new RelativeImagesProcessor());
    }
    
    /**
     * Create highlighter based on configuration options
     *
     * @return Highlighter
     */
    protected function createHighlighter(): Highlighter
    {
        // Check configuration option
        if (\Moinframe\ParaDocs\Options::highlighter() === 'simple') {
            return new \Moinframe\ParaDocs\Highlighter\Simple();
        }
        
        // Check if Phiki is available
        if (class_exists('\Phiki\Phiki')) {
            return new \Moinframe\ParaDocs\Highlighter\Phiki();
        }
        
        // Fallback to Simple if Phiki is not installed
        return new \Moinframe\ParaDocs\Highlighter\Simple();
    }

    /**
     * Add a processor to the collection
     * 
     * @param Processor $processor The processor to add
     * @return self
     */
    public function add(Processor $processor): self
    {
        $this->processors[$processor->getName()] = $processor;
        return $this;
    }

    /**
     * Get a processor by name
     * 
     * @param string $name Processor name
     * @return Processor|null
     */
    public function get(string $name): ?Processor
    {
        return $this->processors[$name] ?? null;
    }

    /**
     * Remove a processor by name
     * 
     * @param string $name Processor name
     * @return self
     */
    public function remove(string $name): self
    {
        if (isset($this->processors[$name])) {
            unset($this->processors[$name]);
        }
        
        return $this;
    }

    /**
     * Get all registered processors
     * 
     * @return array All processors as arrays
     */
    public function toArray(): array
    {
        $result = [];
        
        foreach ($this->processors as $name => $processor) {
            $result[$name] = $processor->toArray();
        }
        
        return $result;
    }

    /**
     * Get all processors
     * 
     * @return array All processors
     */
    public function all(): array
    {
        return $this->processors;
    }
}