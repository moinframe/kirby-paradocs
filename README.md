# Kirby Paradocs

Kirby Paradocs is a plugin for [Kirby CMS](https://getkirby.com) that allows you to create beautiful documentation pages using markdown. The plugin will scan the installed plugins in your Kirby installation and auto-generate a documentation page for each plugin.

The primary use case for this plugin is to easily provide documentation pages for your own plugins. The idea is to combine the flexibility of having the documentation in markdown inside the plugins repository itself with the ease of creating beautiful documentation pages for your users.

## Features

- Automatically generates documentation pages for all installed plugins
- Provides frontend inspired by the Kirby Docs
- (Optional) user authentication to restrict access
- (Optional) syntax highlighting with [Phiki](https://github.com/phiki/phiki)


## Installation

> [!CAUTION]
  This plugin is still in development and should be considered alpha. It will change quite a bit before it reaches a stable version.


### Download

Download and copy this repository to `/site/plugins/kirby-paradocs`.

### Composer

```sh
# Basic installation
composer require moinframe/kirby-paradocs

# With enhanced syntax highlighting
composer require moinframe/kirby-paradocs phiki/phiki
```

### Git submodule

```sh
git submodule add https://github.com/moinframe/kirby-paradocs.git site/plugins/kirby-paradocs
```
