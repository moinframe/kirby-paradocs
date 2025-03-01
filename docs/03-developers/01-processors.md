---
title: Processors
---

Paradocs uses processors to extend the functionality of the markdown parser.

## Built-in Processors

Paradocs comes with several built-in processors:

- `AlertProcessor`: Converts alert syntax (e.g. used in GitHub) to styled alert boxes
- `CodeBlockProcessor`: Handles code blocks with syntax highlighting
- `RelativeImagesProcessor`: Removes relative images


## Add your own

Paradocs allows you to add custom markdown processors to extend the functionality of the markdown parser.
A processor is a class that extends the abstract `Moinframe\ParaDocs\Markdown\Processor` class.

### Creating a Custom Processor

To create a custom processor, you need to:

1. Create a kirby plugin folder with an `index.php`
2. Create a class that extends `Moinframe\ParaDocs\Markdown\Processor` in the plugin
3. Implement the required methods
4. Register it with the parser in `paradocs.parser.ready` hook;

### The Processing Flow

When the markdown parser processes content, it follows these steps for each processor:

1. The `process()` method extracts elements from the content and replaces them with placeholders
2. The markdown content (with placeholders) is converted to HTML
3. The `render()` method converts each extracted element to HTML
4. The rendered elements replace their corresponding placeholders
5. The `postProcess()` method (if implemented) runs on the final HTML

Here is an example of a custom processor:

```php
namespace MySite\Processors;

use Moinframe\ParaDocs\Markdown\Processor;

class CustomProcessor extends Processor
{
    /**
     * Initialize the processor with a unique name
     * This name is used in the placeholder format: <!-- {name}_{index} -->
     */
    public function __construct()
    {
        parent::__construct('custom');
    }

    public function process(string $content): array
    {
        // $content contains the markdown content
        // Use this method to extract elements and replace it with placeholders

        // The method MUST return an array with the following structure:
        return [
            // Required: The modified content with placeholders
            // The placeholders should follow the format: <!-- {processor_name}_{index} -->
            'content' => $processedContent,

            // Required: The extracted elements that will replace the placeholders
            // This is an indexed array where each position corresponds to a placeholder
            'elements' => [
                // Element at index 0 corresponds to placeholder <!-- {processor_name}_0 -->
                [
                    'type' => 'custom', // Optional: identifies the specific type within your processor
                    'content' => 'Original extracted content', // Usually the content to be processed
                    // Add any additional data needed for rendering (language, attributes, etc.)
                ],
                // Additional elements as needed
            ]
        ];
    }

    public function render(array $data): string
    {
        // This method receives a single element from the 'elements' array
        // $data will contain all properties defined for that specific element
        //
        // For example, if your element had these properties:
        // [
        //   'type' => 'custom',
        //   'content' => 'Original content',
        //   'language' => 'php'
        // ]
        //
        // Then you can access them as:
        // $data['type'], $data['content'], $data['language']

        // Transform the data into HTML and return it
        return 'HTML output';
    }

    // Optional: Additional processing after markdown conversion
    public function postProcess(string $html): string
    {
        // Process the HTML after markdown conversion
        // This runs after the markdown has been fully parsed and all placeholders
        // have been replaced with their rendered HTML
        //
        // You can use this method for tasks that need to operate on the
        // final HTML output, such as adjusting paths or adding attributes
        return $html;
    }
}
```

### Example: Processing Pattern Matches

Here's a typical implementation pattern for the `process()` method that extracts content using regular expressions:

```php
public function process(string $content): array
{
    $elements = [];
    $index = 0;

    // Use regex to find and extract content
    $processedContent = preg_replace_callback('/pattern-to-match/', function($matches) use (&$elements, &$index) {
        // Extract data from the matches
        $extracted = [
            'type' => 'some-type',
            'content' => $matches[1] // Captured content from regex
            // Additional properties as needed
        ];

        // Store the extracted data
        $elements[] = $extracted;

        // Return a placeholder that will be replaced later
        // "custom" must match your processor name
        return '<!-- custom_' . ($index++) . ' -->';
    }, $content);

    return [
        'content' => $processedContent,
        'elements' => $elements
    ];
}
```
