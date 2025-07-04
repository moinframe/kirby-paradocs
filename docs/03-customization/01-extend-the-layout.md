---
title: Extend the Layout
---

You can extend the layout used by Paradocs by creating a `paradocs/head`,  `paradocs/header` or `paradocs/footer` snippet in your site's `snippets` folder.

Here's an example of a `paradocs/head` snippet. This snippet will be included before the `<head>` tag of the generated documentation pages.

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


And here's an example of a `paradocs/footer` snippet. This snippet will be included before the `</body>` tag of the generated documentation pages. Using the `<footer>` tag will make sure, the styling is correct.

```html
<!-- file path: site/snippets/paradocs/footer.php -->
<footer>
    <p>Copyright <?= date('Y') ?></p>
</footer>
```
