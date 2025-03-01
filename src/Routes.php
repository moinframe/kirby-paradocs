<?php

namespace Moinframe\ParaDocs;

use Moinframe\ParaDocs\Options;
use Moinframe\ParaDocs\App;

class Routes
{
    public static function register(): array
    {

        return [
            [
                'pattern' => Options::slug(),
                'action' => function () {
                    if (true === Options::hideIndex()) {
                        return false;
                    }
                    if (!Options::public() && !kirby()->user()) {
                        return false;
                    }
                    return App::create()->render(['menu' => null]);
                }
            ],
            [
                'pattern' => Options::slug() . '/(:all)',
                'action' => function ($path) {
                    if (!Options::public() && !kirby()->user()) {
                        return false;
                    }
                    $indexPage = App::create();
                    $page = $indexPage->index()->find(Options::slug() . "/" . $path);
                    if (!$page) return;
                    $plugin = $page->parents()->flip()->nth(1) ?? $page;
                    return $page->render(['menu' => $plugin->children(), 'plugin' => $plugin]);
                }
            ]
        ];
    }
}
