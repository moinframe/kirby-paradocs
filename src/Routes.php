<?php

namespace Moinframe\ParaDocs;

use Kirby\Filesystem\F;
use Kirby\Http\Response;
use Kirby\Toolkit\Mime;
use Moinframe\ParaDocs\Options;
use Moinframe\ParaDocs\App;
use Moinframe\ParaDocs\Plugins;

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
				'pattern' => Options::slug() . '/media/(:any)/(:all)',
				'action' => function (string $pluginSlug, string $filePath) {

					if (!Options::public() && kirby()->user() === null) {
						return false;
					}

					// Only serve allowed image extensions
					$allowedExtensions = ['png', 'jpg', 'jpeg', 'gif', 'svg', 'webp', 'ico', 'avif'];
					$extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
					if (!in_array($extension, $allowedExtensions, true)) {
						return false;
					}

					$plugin = Plugins::get($pluginSlug);
					if ($plugin === null) {
						return false;
					}

					// Try docs directory first, then plugin root
					$docsRoot = $plugin['config']['root'] ?? 'docs';
					$pluginRoot = realpath($plugin['root']);

					if ($pluginRoot === false) {
						return false;
					}

					$absolutePath = $plugin['root'] . '/' . $docsRoot . '/' . $filePath;
					$realPath = realpath($absolutePath);

					// Fall back to plugin root (e.g. images referenced from README.md)
					if ($realPath === false) {
						$absolutePath = $plugin['root'] . '/' . $filePath;
						$realPath = realpath($absolutePath);
					}

					if ($realPath === false) {
						return false;
					}

					if (!str_starts_with($realPath, $pluginRoot . '/')) {
						return false;
					}

					$mime = Mime::type($realPath);
					$content = F::read($realPath);

					if ($content === false) {
						return false;
					}

					return new Response(
						$content,
						$mime ?? 'application/octet-stream',
						200
					);
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

					$indexPage = App::createForPath($path);
					if ($indexPage === null) return;
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
