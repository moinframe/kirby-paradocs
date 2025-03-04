---
title: Markdown
---

Kirby Paradocs supports standard markdown syntax. Some additional features are available.

## Frontmatter

Frontmatter is a YAML block at the top of a markdown file that contains metadata about the page. It is used to provide additional information about the page. You can use it to set the page title.

```markdown
---
title: My Page
---
```

## Relative Images

Relative images will be removed from the rendered documentation page. You can however use hosted images in your documentation pages.

```markdown
![Alt text](https://example.com/image.png)
```

## Code Blocks

Code blocks will be syntax highlighted using the configured highlighter.

```php
return [
  'my' => 'codeblock'
];
```


## Alerts (GitHub-style)

You can use GitHub-style alerts in your documentation pages. These alerts will be rendered as a box with a matching icon and the content will be parsed as markdown.

```markdown
> [!NOTE] This is a note

> [!WARNING] This is a warning

> [!IMPORTANT] This is an important note

> [!TIP] This is a tip

> [!CAUTION] This is a caution

```

> [!NOTE] This is a note

> [!WARNING] This is a warning

> [!IMPORTANT] This is an important note

> [!TIP] This is a tip

> [!CAUTION] This is a caution

