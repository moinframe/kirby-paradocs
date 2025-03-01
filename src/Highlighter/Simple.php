<?php

namespace Moinframe\ParaDocs\Highlighter;

class Simple implements Interface\Highlighter
{
    /**
     * Highlight code with basic HTML/CSS
     *
     * @param string $code The code to highlight
     * @param string $language The language identifier
     * @return string HTML for the highlighted code
     */
    public function highlight(string $code, string $language): string
    {
        // Simply escape the code and wrap it in pre/code tags
        $escapedCode = htmlspecialchars($code);

        return $this->wrapInHtml($escapedCode, $language);
    }

    /**
     * Wrap the code in HTML with the appropriate language class
     *
     * @param string $code The code to wrap
     * @param string $language The language identifier
     * @return string HTML wrapped code
     */
    private function wrapInHtml(string $code, string $language): string
    {
        return '<pre class="simple-highlight"><code class="language-' . $language . '">' . $code . '</code></pre>';
    }
}
