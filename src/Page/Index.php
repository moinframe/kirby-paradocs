<?php

namespace Moinframe\ParaDocs;

use Kirby\Cms\Page;

/**
 * Model for plugin overview page
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
