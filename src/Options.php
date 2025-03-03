<?php

namespace Moinframe\ParaDocs;

class Options
{
    /**
     * Get the docs slug
     * @return string
     */
    public static function slug(): string
    {
        return option('moinframe.kirby-paradocs.index.slug', 'docs');
    }

    /**
     * Get the docs title
     * @return string
     */
    public static function title(): string
    {
        return option('moinframe.kirby-paradocs.index.title', 'Documentation');
    }

    /**
     * Check if docs page should be accessible
     * @return bool
     */
    public static function hideIndex(): bool
    {
        return option('moinframe.kirby-paradocs.index.hide', false);
    }

    /**
     * Allow all plugins to be included
     * @return bool
     * */
    public static function includeAll(): bool
    {
        return option('moinframe.kirby-paradocs.includeAll', false);
    }

    /**
     * Safelist plugins to be included in the docs
     * @return array list of plugin names
     */
    public static function safelist(): array | null
    {
        return option('moinframe.kirby-paradocs.safelist', null);
    }

    /**
     * Check if the docs should be public
     * @return bool
     */
    public static function public(): bool
    {
        return option('moinframe.kirby-paradocs.public', false);
    }

    /**
     * Get the syntax highlighter to use
     * @return string
     */
    public static function highlighter(): string
    {
        return option('moinframe.kirby-paradocs.highlighter', 'phiki');
    }

    /**
     * Check if caching is enabled
     * @return bool
     */
    public static function cache(): bool
    {
        return option('moinframe.kirby-paradocs.cache', true);
    }
}
