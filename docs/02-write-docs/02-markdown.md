---
title: Markdown
---

Kirby Paradocs supports standard markdown syntax. Some additional features are available.

## Frontmatter

Frontmatter is a YAML block at the top of a markdown file that contains metadata about the page. It is used to provide additional information about the page. You can use it to set the page title and description.

```markdown
---
title: My Page
description: A brief description of the page content
---
```

The `description` field is used for meta tags on the generated page.

## Images

Relative images will be rewritten and served via a media route.

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

When using the Phiki highlighter, the following language aliases are supported: `js` (JavaScript), `ts` (TypeScript), `py` (Python), `c++` (C++), `c#` (C#), `sh`/`bash`/`shell` (Shell), `md` (Markdown), and `yml` (YAML).


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

