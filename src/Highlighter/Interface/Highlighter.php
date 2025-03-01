<?php

namespace Moinframe\ParaDocs\Highlighter\Interface;

interface Highlighter
{
    /**
     * Highlight code with syntax highlighting
     *
     * @param string $code The code to highlight
     * @param string $language The language identifier
     * @return string HTML for the highlighted code
     */
    public function highlight(string $code, string $language): string;
}
