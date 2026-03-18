<?php

namespace Moinframe\ParaDocs;

use Kirby\Plugin\Plugin;
use Kirby\Filesystem\F;
use Kirby\Toolkit\Str;
use Kirby\Toolkit\A;

class Plugins
{
    /**
     * Return paradocs config path
     */
    public static function configPath(Plugin $plugin): string
    {
        return $plugin->root() . '/.paradocs.json';
    }

    /**
     * Check if plugin exists in safelist
     */
    public static function isInSafelist(Plugin $plugin): bool
    {
        $safelist = Options::safelist();
        return $safelist !== null && A::has($safelist, $plugin->id());
    }

    /**
     * Check if plugin is supported
     */
    public static function isSupported(Plugin $plugin): bool
    {
        $configPath = self::configPath($plugin);
        $readmePath = $plugin->root() . '/README.md';
        $hasSafelist = null !== Options::safelist();
        // If safelist active: either config or readme path must exists and plugin must be safelisted. If not, config or readme path must exist.
        return $hasSafelist ? (F::exists($configPath) || F::exists($readmePath)) && self::isInSafelist($plugin) : (F::exists($configPath) || F::exists($readmePath));
    }

    /**
     * Get all plugins
     * @return array<string, mixed>
     */
    public static function getAll(): array
    {
        $plugins = [];

        foreach (kirby()->plugins() as $key => $plugin) {

            if (!self::isSupported($plugin)) continue;

            $id = Str::slug($key);
            $configContent = F::read(self::configPath($plugin));
            $config = $configContent !== false ? json_decode($configContent, true) : null;
            $plugins[$id] = [
                'id' => $id,
                'config' => $config,
                'info' => $plugin->info(),
                'root' => $plugin->root(),
                'plugin' => $plugin
            ];
        }

        return $plugins;
    }

    /**
     * Get details for a specific plugin with documentation
     * @return array<string, mixed>|null
     */
    public static function get(string $pluginName): ?array
    {
        return self::getAll()[$pluginName] ?? null;
    }
}
