---
title: Write documentation
---

Kirby Paradocs supports plugins that have a `README.md` file by default. You can also add a more complex documentation structure in a folder named `docs` in the root of your plugin. This folder can contain any number of markdown files and folders.

## Add your own documentation

To add your own documentation, create a folder in `site/plugins` and add a `index.php` and a `README.md` file.

```php
// file path: site/plugins/your-plugin/index.php

Kirby::plugin('your-name/your-plugin', []);
```

```markdown
# My Plugin
This is my plugin documentation
```

The documentation for your plugin will be accessible at
`https://yourdomain.com/docs/your-name-your-plugin`.

## Configuration

To change the default configuration, create a `.paradocs.json` file in the root of your plugin. This file should contain a JSON object with the following properties:

```json
{
  // The title of your plugin
  "title": "My Plugin",
  // The description of your plugin
  "description": "This is my plugin documentation",
  // The folder name where your documentation is located
  "root": "my-docs-folder",
  // Optional logo file placed in the assets folder
  "logo" : "logo.svg"
}
```
