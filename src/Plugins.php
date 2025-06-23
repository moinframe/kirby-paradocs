<?php

namespace Moinframe\ParaDocs;

use Kirby\Filesystem\F;
use Kirby\Toolkit\Str;
use Kirby\Toolkit\A;

class Plugins
{
    /**
     * Return paradocs config path
     * @param mixed $plugin
     * @return string
     */
    public static function configPath($plugin): string
    {
        return $plugin->root() . '/.paradocs.json';
    }

    /**
     * Check if plugin exists in safelist
     * @param mixed $plugin
     * @return bool
     */
    public static function isInSafelist($plugin): bool
    {
        $safelist = Options::safelist();
        return $safelist !== null && A::has($safelist, $plugin->id());
    }

    /**
     * Check if plugin is supported
     * @param mixed $plugin
     * @return bool
     */
    public static function isSupported($plugin): bool
    {
        $configPath = Plugins::configPath($plugin);
        $readmePath = $plugin->root() . '/README.md';
        $hasSafelist = null !== Options::safelist();
        // If safelist active: either config or readme path must exists and plugin must be safelisted. If not, config or readme path must exist.
        return $hasSafelist ? (F::exists($configPath) || F::exists($readmePath)) && Plugins::isInSafelist($plugin) : (F::exists($configPath) || F::exists($readmePath));
    }

    /**
     * Get all plugins
     * @return array
     */
    /**
     * Get all plugins
     * @return array<string, mixed>
     */
    public static function getAll(): array
    {
        $plugins = [];

        foreach (kirby()->plugins() as $key => $plugin) {

            if (!Plugins::isSupported($plugin)) continue;

            $id = Str::slug($key);
            $configContent = F::read(Plugins::configPath($plugin));
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
     */
    /**
     * Get details for a specific plugin with documentation
     * @return array<string, mixed>|null
     */
    public static function get(string $pluginName): ?array
    {
        return Plugins::getAll()[$pluginName];
    }
}
