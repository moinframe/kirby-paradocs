<?php

namespace Moinframe\ParaDocs;

use Moinframe\ParaDocs\Options;
use Moinframe\ParaDocs\App;

class Routes
{
	/**
	 * @return array<int, array<string, mixed>>
	 */
	public static function register(): array
	{

		return [
			[
				'pattern' => Options::slug(),
				'action' => function () {

					if (true === Options::hideIndex()) {
						return false;
					}

					if (!Options::public() && kirby()->user() === null) {
						return false;
					}

					$kirby = kirby();
					$cache = $kirby->cache('moinframe.paradocs');
					$cacheEnabled = Options::cache();
					$cacheKey = 'route_index';

					// Try to get rendered page from cache
					if ($cacheEnabled && $rendered = $cache->get($cacheKey)) {
						return $rendered;
					}

					$rendered = App::create()->render(['menu' => null]);

					// Save to cache
					if ($cacheEnabled) {
						$cache->set($cacheKey, $rendered, Options::cacheTime());
					}

					return $rendered;
				}
			],
			[
				'pattern' => Options::slug() . '/(:all)',
				'action' => function ($path) {

					if (!Options::public() && kirby()->user() === null) {
						return false;
					}

					$kirby = kirby();
					$cache = $kirby->cache('moinframe.paradocs');
					$cacheEnabled = Options::cache();
					$cacheKey = 'route_page_' . md5($path);

					// Try to get rendered page from cache
					if ($cacheEnabled && $rendered = $cache->get($cacheKey)) {
						return $rendered;
					}

					$indexPage = App::create();
					$page = $indexPage->index()->find(Options::slug() . "/" . $path);
					if ($page === null) return;
					$plugin = $page->parents()->flip()->nth(1) ?? $page;

					$rendered = $page->render(['menu' => $plugin->children(), 'plugin' => $plugin]);

					// Save to cache
					if ($cacheEnabled) {
						$cache->set($cacheKey, $rendered, Options::cacheTime());
					}

					return $rendered;
				}
			]
		];
	}
}
