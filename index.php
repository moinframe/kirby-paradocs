<?php

use Kirby\Cms\App as Kirby;
use Kirby\Filesystem\F;

F::loadClasses([
    'Moinframe\\ParaDocs\\App' => 'src/App.php',
    'Moinframe\\ParaDocs\\Options' => 'src/Options.php',
    'Moinframe\\ParaDocs\\Plugins' => 'src/Plugins.php',
    'Moinframe\\ParaDocs\\Routes' => 'src/Routes.php',
    'Moinframe\\ParaDocs\\Page\\IndexPage' => 'src/Page/IndexPage.php',
    'Moinframe\\ParaDocs\\Page\\PluginPage' => 'src/Page/PluginPage.php',
    'Moinframe\\ParaDocs\\Markdown\\Parser' => 'src/Markdown/Parser.php',
    'Moinframe\\ParaDocs\\Markdown\\Processor' => 'src/Markdown/Processor.php',
    'Moinframe\\ParaDocs\\Markdown\\ProcessorCollection' => 'src/Markdown/ProcessorCollection.php',
    'Moinframe\\ParaDocs\\Markdown\\Processors\\AlertProcessor' => 'src/Markdown/Processors/AlertProcessor.php',
    'Moinframe\\ParaDocs\\Markdown\\Processors\\CodeBlockProcessor' => 'src/Markdown/Processors/CodeBlockProcessor.php',
    'Moinframe\\ParaDocs\\Markdown\\Processors\\RelativeImagesProcessor' => 'src/Markdown/Processors/RelativeImagesProcessor.php',
    'Moinframe\\ParaDocs\\Highlighter\\Interface\\Highlighter' => 'src/Highlighter/Interface/Highlighter.php',
    'Moinframe\\ParaDocs\\Highlighter\\Phiki' => 'src/Highlighter/Phiki.php',
    'Moinframe\\ParaDocs\\Highlighter\\Simple' => 'src/Highlighter/Simple.php',
], __DIR__);

Kirby::plugin('moinframe/paradocs', [
    'options' => [
        'cache' => true
    ],
    'pageModels' => [
        'paradocs-index' => Moinframe\ParaDocs\Page\IndexPage::class,
        'paradocs-plugin' => Moinframe\ParaDocs\Page\PluginPage::class,
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
