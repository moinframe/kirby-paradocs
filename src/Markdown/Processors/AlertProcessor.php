<?php

namespace Moinframe\ParaDocs\Markdown\Processors;

use Moinframe\ParaDocs\Markdown\Processor;

/**
 * Processor for alert boxes
 */
class AlertProcessor extends Processor
{
    /**
     * Regular expression pattern for matching alerts
     */
    protected string $pattern = '/> \[!([A-Z]+)\](.*?)(?=\n\n|\n$|$)/s';

    /**
     * Create a new alert processor
     */
    public function __construct()
    {
        parent::__construct('alerts');
    }

    /**
     * Process content and extract alerts
     *
     * @param string $content Content to process
     * @return array Processed content and extracted elements
     */
    public function process(string $content): array
    {
        $elements = [];
        $name = $this->getName();

        // Replace alerts with placeholders using callback
        $processedContent = preg_replace_callback($this->pattern, function ($matches) use (&$elements, $name) {
            $elements[] = [
                'type' => strtolower($matches[1]),
                'content' => kirby()->markdown(trim($matches[2]))
            ];

            return "<!-- {$name}_" . (count($elements) - 1) . " -->";
        }, $content);

        return [
            'content' => $processedContent,
            'elements' => $elements
        ];
    }

    /**
     * Render an alert to HTML
     *
     * @param array $data Extracted data
     * @return string HTML output
     */
    public function render(array $data): string
    {
        return snippet('paradocs/alert', [
            'type' => $data['type'],
            'content' => $data['content']
        ], true);
    }
}
