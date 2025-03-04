---
title: Example
---

Here's an example that creates a custom processor for handling YouTube embeds.

```php
// file path: site/plugins/your-plugin/lib/YoutubeProcessor.php

namespace MySite\Processors;

use Moinframe\ParaDocs\Markdown\Processor;

class YouTubeProcessor extends Processor
{
    public function __construct()
    {
        // Each processor needs a unique name
        parent::__construct('youtube');
    }

    public function process(string $content): array
    {
        // Pattern to match YouTube embeds: @youtube[VIDEO_ID]
        $pattern = '/@youtube\[([a-zA-Z0-9_-]+)\]/';
        $elements = [];
        $name = $this->getName();

        // Match all YouTube embeds
        preg_match_all($pattern, $content, $matches, PREG_OFFSET_CAPTURE);

        if (empty($matches[0])) {
            return ['content' => $content, 'elements' => []];
        }

        $positions = [];

        // Extract each YouTube video ID
        foreach ($matches[0] as $index => $match) {
            $elements[] = [
                'videoId' => $matches[1][$index][0]
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

    public function render(array $data): string
    {
        $videoId = $data['videoId'];

        return '<div class="youtube-embed">
            <iframe width="560" height="315"
                src="https://www.youtube.com/embed/' . $videoId . '"
                frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen>
            </iframe>
        </div>';
    }
}
```

### Registering a Custom Processor

To register your custom processor with Paradocs, add it in your site's `config.php` file:

```php
// file path: site/plugins/your-plugin/index.php

F::loadClasses([
    'MySite\\Processors\\YouTubeProcessor' => __DIR__ . '/lib/YoutubeProcessors.php'
]);

Kirby::plugin('your-name/your-plugin', [
    'hooks' => [
        'paradocs.parser.ready' => function($parser) {
            // Add your custom processor
            $parser->addProcessor(new MySite\Processors\YouTubeProcessor());

            // You can also remove default processors if needed
            // $parser->removeProcessor('codeBlocks');
        }
    ]
]);
```

### Using the Custom Processor

After registering your processor, you can use it in your markdown files:

```markdown
# My Page with YouTube Video

Here's a great video:

@youtube[dQw4w9WgXcQ]
```
