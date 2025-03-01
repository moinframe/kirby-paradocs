<?php

namespace Moinframe\ParaDocs\Page;

use Kirby\Cms\Page;
use Moinframe\ParaDocs\Plugins;

/**
 * Model for plugins overview page
 **/
class IndexPage extends Page
{
    /**
     * Get all plugins with documentation
     */
    public function plugins(): array
    {
        return Plugins::getAll();
    }
}
