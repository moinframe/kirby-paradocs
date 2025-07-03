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
		return option('moinframe.paradocs.index.slug', 'docs');
	}

	/**
	 * Get the docs title
	 * @return string
	 */
	public static function title(): string
	{
		return option('moinframe.paradocs.index.title', 'Documentation');
	}

	/**
	 * Check if docs page should be accessible
	 * @return bool
	 */
	public static function hideIndex(): bool
	{
		return option('moinframe.paradocs.index.hide', false);
	}

	/**
	 * Safelist plugins to be included in the docs
	 * @return array<string>|null list of plugin names
	 */
	public static function safelist(): ?array
	{
		return option('moinframe.paradocs.safelist', null);
	}

	/**
	 * Check if the docs should be public
	 * @return bool
	 */
	public static function public(): bool
	{
		return option('moinframe.paradocs.public', false);
	}

	/**
	 * Get the syntax highlighter to use
	 * @return string
	 */
	public static function highlighter(): string
	{
		return option('moinframe.paradocs.highlighter', 'phiki');
	}

	/**
	 * Check if caching is enabled
	 * @return bool
	 */
	public static function cache(): bool
	{
		$cache = option('moinframe.paradocs.cache', true);
		if (is_array($cache) && isset($cache['active'])) {
			return $cache['active'];
		}
		return $cache;
	}

	/**
	 * Get cache expiration time
	 * @return int
	 */
	public static function cacheTime(): int
	{
		return option('moinframe.paradocs.cache.expire', 60 * 24);
	}
}
