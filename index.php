<?php

use Kirby\Cms\App as Kirby;

@include_once __DIR__ . '/vendor/autoload.php';

Kirby::plugin('moinframe/paradocs', [
    'options' => [
        'cache' => true
    ],
    'routes' => Moinframe\ParaDocs\Routes::register(),
    'templates' => [
        'paradocs-index' => __DIR__ . '/templates/paradocs-index.php',
        'paradocs-plugin' => __DIR__ . '/templates/paradocs-plugin.php',
        'paradocs-plugin-page' => __DIR__ . '/templates/paradocs-plugin-page.php',
        'paradocs-plugin-directory' => __DIR__ . '/templates/paradocs-plugin-directory.php'
    ],
    'snippets' => [
        'paradocs/layout' => __DIR__ . '/snippets/layout.php',
        'paradocs/header' => __DIR__ . '/snippets/header.php',
        'paradocs/footer' => __DIR__ . '/snippets/footer.php',
        'paradocs/menu' => __DIR__ . '/snippets/menu.php',
        'paradocs/alert' => __DIR__ . '/snippets/alert.php',
        'paradocs/codeblock' => __DIR__ . '/snippets/codeblock.php',
        'paradocs/head' => __DIR__ . '/snippets/head.php',
        'paradocs/foot' => __DIR__ . '/snippets/foot.php'
    ],
    'hooks' => [
        'paradocs.parser.ready' => function ($parser) {
            // This hook is triggered when a parser is initialized
            // Developers can use this to add or remove processors
        }
    ]
]);
