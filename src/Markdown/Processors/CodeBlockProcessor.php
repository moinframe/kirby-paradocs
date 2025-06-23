<?php

namespace Moinframe\ParaDocs\Markdown\Processors;

use Moinframe\ParaDocs\Markdown\Processor;
use Moinframe\ParaDocs\Highlighter\Interface\Highlighter;

/**
 * Processor for code blocks
 */
class CodeBlockProcessor extends Processor
{
    /**
     * Syntax highlighter
     */
    protected Highlighter $highlighter;

    /**
     * Regular expression pattern for matching code blocks
     */
    protected string $pattern = '/```(\w+)?\n(.*?)\n```/s';

    /**
     * Create a new code block processor
     *
     * @param Highlighter $highlighter Syntax highlighter
     */
    public function __construct(Highlighter $highlighter)
    {
        parent::__construct('codeBlocks');
        $this->highlighter = $highlighter;
    }

    /**
     * Process content and extract code blocks
     *
     * @param string $content Content to process
     * @return array{content: string, elements: array<mixed>} Processed content and extracted elements
     */
    public function process(string $content): array
    {
        $elements = [];
        $pattern = $this->pattern;
        $name = $this->getName();

        // Match code blocks with position information
        preg_match_all($pattern, $content, $matches, PREG_OFFSET_CAPTURE);

        if (count($matches[0]) === 0) {
            return ['content' => $content, 'elements' => []];
        }

        $positions = [];

        // Extract each code block
        foreach ($matches[0] as $index => $match) {
            $elements[] = [
                'language' => strtolower(trim($matches[1][$index][0] ?? 'txt')),
                'code' => $matches[2][$index][0]
            ];

            $positions[] = [
                'start' => $match[1],
                'length' => strlen($match[0])
            ];
        }

        // Replace matches with placeholders (in reverse to maintain positions)
        $processedContent = $content;
        for ($i = count($positions) - 1; $i >= 0; $i--) {
            $placeholder = "<!-- {$name}_{$i} -->";
            $processedContent = substr_replace(
                $processedContent,
                $placeholder,
                $positions[$i]['start'],
                $positions[$i]['length']
            );
        }

        return [
            'content' => $processedContent,
            'elements' => $elements
        ];
    }

    /**
     * Render a code block to HTML
     *
     * @param array<string, mixed> $data Extracted data
     * @return string HTML output
     */
    public function render(array $data): string
    {
        $rendered = snippet('paradocs/codeblock', [
            'highlighter' => $this->highlighter,
            'language' => $data['language'],
            'code' => $data['code']
        ], true);
        
        if (is_string($rendered)) {
            return $rendered;
        }
        
        return '';
    }
}
