---
title: Getting started
---

## Install the plugin

### Download

Download and copy this repository to `/site/plugins/paradocs`.

### Composer

```bash
composer require moinframe/kirby-paradocs

# With enhanced syntax highlighting
composer require moinframe/kirby-paradocs phiki/phiki
```

### Git submodule

```sh
git submodule add https://github.com/moinframe/kirby-paradocs.git site/plugins/paradocs
```

## Usage

Kirby Paradocs automatically generates documentation pages for all installed plugins. By default, the documentation is located at `https://yourdomain.com/docs`.

To access the documentation, simply navigate to the URL of your site and append `/docs` to the end of the URL.  If you do not want to have an index page, you can set the `index.hide` option to `true` in your `site/config/config.php` file. You can reach the documentation for a specific plugin by navigating to the URL of your site and appending `/docs/plugin-name` to the end of the URL.

> [!NOTE]
If you did not change the default configuration, you need to be **logged in to access** the documentation, otherwise you will be redirected to an error page. With the `public` set to true, anyone can access the documentation.


## Caching

The plugin caches the generated documentation pages to speed up page load times. If you want to disable caching, you can set the `cache` option to `false` in your `site/config/config.php` file. By default, the content is cached for 24 hours.
