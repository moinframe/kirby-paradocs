# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Kirby Paradocs is a Kirby CMS plugin that automatically generates documentation pages from markdown files located within other Kirby plugins. It scans installed plugins for documentation and creates a unified documentation site with a clean frontend.

## Development Commands

### Release Management
```bash
npm run release          # Create new release with conventional changelog
```

### Code Quality
```bash
composer run analyse    # Run static analysis (PHPStan with strict rules)
```

### Package Management
```bash
pnpm install            # Install Node.js dependencies (preferred over npm)
composer install        # Install PHP dependencies
```

## Architecture Overview

The plugin follows a modular architecture:

- **Routes.php**: Handles `/docs` URL routing
- **App.php**: Main orchestrator that builds page hierarchies from plugin docs
- **Plugins.php**: Scans installed plugins for documentation files
- **Markdown/**: Complete markdown processing system with processors for alerts, code blocks, and relative images
- **Highlighter/**: Syntax highlighting (supports Phiki or simple fallback)
- **Page/**: Custom Kirby page types for documentation structure

## Key Configuration

Plugin configuration in `site/config/config.php`:
```php
'moinframe.kirby-paradocs' => [
    'index' => [
        'slug' => 'docs',           // URL slug for docs (default: 'docs')
        'title' => 'Documentation', // Title for docs index (default: 'Documentation')
        'hide' => false             // Hide docs index page (default: false)
    ],
    'safelist' => null,             // Array of plugin names to include, null = all (default: null)
    'public' => false,              // Make docs publicly accessible (default: false)
    'highlighter' => 'phiki',       // Syntax highlighter: 'phiki' or 'simple' (default: 'phiki')
    'cache' => true,                // Enable caching (default: true)
    'cache' => [                    // Alternative cache configuration
        'active' => true,           // Enable/disable cache
        'expire' => 1440            // Cache expiration in minutes (default: 1440 = 24 hours)
    ]
]
```

## Plugin Documentation Structure

Plugins are discovered through:
- `README.md` file in plugin root
- `.paradocs.json` configuration file
- Documentation files in `/docs` subdirectory (configurable)

Documentation files support YAML frontmatter and hierarchical structure with `index.md` files for directory pages.

## Important Notes

- Uses pnpm as package manager (version 10.12.2)
- Node.js version pinned to v22 (see `.nvmrc`)
- Requires Kirby CMS v4.6+ or v5.0+
- PHPStan configured with strict rules for code quality
- Uses conventional commits for changelog generation
