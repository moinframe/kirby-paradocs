<?php

use Kirby\Cms\App as Kirby;

@include_once __DIR__ . '/vendor/autoload.php';

Kirby::plugin('moinframe/kirby-paradocs', [
    'routes' => Moinframe\ParaDocs\Routes::register(),
    'templates' => [
        'paradocs-index' => __DIR__ . '/templates/paradocs-index.php',
        'paradocs-plugin' => __DIR__ . '/templates/paradocs-plugin.php',
        'paradocs-plugin-page' => __DIR__ . '/templates/paradocs-plugin-page.php',
        'paradocs-plugin-directory' => __DIR__ . '/templates/paradocs-plugin-directory.php'
    ],
    'snippets' => [
        'paradocs/menu' => __DIR__ . '/snippets/menu.php',
        'paradocs/layout' => __DIR__ . '/snippets/layout.php',
        'paradocs/alert' => __DIR__ . '/snippets/alert.php',
        'paradocs/codeblock' => __DIR__ . '/snippets/codeblock.php'
    ],
    'hooks' => [
        'paradocs.parser.ready' => function ($parser) {
            // This hook is triggered when a parser is initialized
            // Developers can use this to add or remove processors
        }
    ]
]);
