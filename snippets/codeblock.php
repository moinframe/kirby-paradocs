<?php

/**
 * Default code block snippet
 *
 * @param string $code The code content
 * @param string $language The code language (for syntax highlighting)
 * @param Highlighter $highlighter The syntax highlighter instance
 */

// Generate HTML for code block
$html = $highlighter->highlight($code, $language);
?>

<?= $html ?>
