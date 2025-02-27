<?php

use Kirby\Filesystem\F;
use Kirby\Cms\App as Kirby;

F::loadClasses([
    'moinframe\\paradocs\\plugins' =>  '/src/Plugins/Plugins.php',
    'moinframe\\paradocs\\markdownparser' =>  '/src/Markdown/Parser.php',
    'moinframe\\paradocs\\app' =>  '/src/App/App.php',
    'moinframe\\paradocs\\options' =>  '/src/Options/Options.php',
    'moinframe\\paradocs\\routes' =>  '/src/Routes/Routes.php',
    'moinframe\\paradocs\\indexpage' =>  '/src/Page/Index.php',
    'moinframe\\paradocs\\pluginpage' =>  '/src/Page/Plugin.php',
], __DIR__);

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
        'paradocs/layout' => __DIR__ . '/snippets/layout.php'
    ]
]);
