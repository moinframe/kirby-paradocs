<?php

namespace Moinframe\ParaDocs\Highlighter;

use Phiki\Phiki as PhikiHighlighter;
use Phiki\Grammar\Grammar;
use Phiki\Theme\Theme;

/**
 * Phiki implementation of the syntax highlighter
 */
class Phiki implements Interface\Highlighter
{
    /**
     * The Phiki syntax highlighter instance
     */
    protected PhikiHighlighter $phiki;

    /**
     * Default theme for code highlighting
     */
    protected Theme $theme;

    /**
     * Initialize the highlighter with Phiki
     *
     * @param Theme|null $theme Custom theme (optional)
     */
    public function __construct(?Theme $theme = null)
    {
        $this->phiki = new PhikiHighlighter();
        $this->theme = $theme ?? Theme::GithubDark;
    }

    /**
     * Highlight code with Phiki syntax highlighting
     *
     * @param string $code The code to highlight
     * @param string $language The language identifier
     * @return string HTML for the highlighted code
     */
    public function highlight(string $code, string $language): string
    {
        $grammar = $this->getGrammarByLanguage($language);

        try {
            $highlighted = $this->phiki->codeToHtml($code, $grammar, $this->theme);
            return $this->wrapCodeHtml($highlighted, $language);
        } catch (\Exception $e) {
            // If specific grammar fails, try with Txt grammar as fallback
            if ($grammar !== Grammar::Txt) {
                try {
                    $highlighted = $this->phiki->codeToHtml($code, Grammar::Txt, $this->theme);
                    return $this->wrapCodeHtml($highlighted, $language);
                } catch (\Exception $e) {
                    // If all highlighting fails, return without highlighting
                    return $this->wrapCodeHtml(htmlspecialchars($code), $language, false);
                }
            }

            // If Txt grammar fails directly
            return $this->wrapCodeHtml(htmlspecialchars($code), $language, false);
        }
    }

    /**
     * Wrap highlighted code in HTML tags
     *
     * @param string $code The highlighted code or escaped plain text
     * @param string $language The language identifier for class
     * @param bool $highlighted Whether the code was successfully highlighted
     * @return string HTML wrapped code
     */
    private function wrapCodeHtml(string $code, string $language, bool $highlighted = true): string
    {
        if ($highlighted) {
            return $code;
        }

        return '<pre><code class="language-' . $language . '">' . $code . '</code></pre>';
    }

    /**
     * Get the corresponding Grammar enum case for a language identifier
     *
     * @param string $language The language identifier
     * @return Grammar The matching Grammar enum case
     */
    private function getGrammarByLanguage(string $language): Grammar
    {
        // Map common aliases to their Grammar enum equivalents
        $languageAliases = [
            'js' => 'javascript',
            'ts' => 'typescript',
            'py' => 'python',
            'c++' => 'cpp',
            'c#' => 'csharp',
            'sh' => 'shellscript',
            'bash' => 'shellscript',
            'shell' => 'shellscript',
            'md' => 'markdown',
            'yml' => 'yaml',
        ];

        // Normalize language name through aliases
        $normalizedLanguage = $languageAliases[$language] ?? $language;

        // Try to match with a Grammar enum case
        foreach (Grammar::cases() as $case) {
            if ($case->value === $normalizedLanguage) {
                return $case;
            }
        }

        // Default to Txt if no match found
        return Grammar::Txt;
    }
}
