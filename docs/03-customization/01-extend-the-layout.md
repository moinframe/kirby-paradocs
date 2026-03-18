---
title: Extend the Layout
---

You can extend the layout used by Paradocs by creating `paradocs/head`, `paradocs/header`, `paradocs/footer`, or `paradocs/foot` snippets in your site's `snippets` folder.

Here's an example of a `paradocs/head` snippet. This snippet will be included at the end of the `<head>` tag (after CSS) of the generated documentation pages.

```php
// file path: site/snippets/paradocs/head.php

echo 'Custom code here';
```

Here's an example of a `paradocs/header` snippet. It will be added after the opening `<body>` tag. Using the `<header>` tag will make sure, the styling is correct.

```html
<!-- file path: site/snippets/paradocs/header.php -->
<header>
    <h1>My Site</h1>
</header>
```


And here's an example of a `paradocs/footer` snippet. This snippet will be included after the main content area. Using the `<footer>` tag will make sure, the styling is correct.

```html
<!-- file path: site/snippets/paradocs/footer.php -->
<footer>
    <p>Copyright <?= date('Y') ?></p>
</footer>
```

The `paradocs/foot` snippet is rendered after the footer, just before the closing `</body>` tag. This is useful for adding scripts or tracking code.

```php
// file path: site/snippets/paradocs/foot.php

echo '<script src="https://example.com/analytics.js"></script>';
```
