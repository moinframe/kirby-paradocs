<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= $page->title() ?><?= $page->parent() ? " - " . $page->parent()->title() : "" ?></title>
    <?php if ($page->description()->isNotEmpty()): ?>
        <meta name="description" content="<?= $page->description() ?>">
    <?php endif ?>

    <meta property="og:title" content="<?= $page->title() ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= $page->url() ?>">
    <?php if ($page->description()->isNotEmpty()): ?>
        <meta property="og:description" content="<?= $page->description() ?>">
    <?php endif ?>

    <?= css(Kirby\Cms\App::plugin('moinframe/kirby-paradocs')->asset('index.css')) ?>
</head>

<body data-template="<?= $page->intendedTemplate()->name() ?>">
    <?php snippet('paradocs/header') ?>
    <aside>
        <div class="sidebar">
            <?php if (isset($plugin)): ?>
                <header>
                    <?php if ($plugin->logo()->isNotEmpty()): ?>
                        <a href="<?= $plugin->url() ?>" aria-label="<?= $plugin->title() ?>"><img src="<?= $plugin->logo(); ?>" alt="" aria-hidden /></a>
                    <?php else: ?>
                        <p><a href="<?= $plugin->url() ?>"><strong><?= $plugin->title() ?></strong></a></p>
                    <?php endif; ?>
                </header>
                <?php if ($menu): ?>
                    <section class="mt-md">
                        <nav>
                            <?php snippet('paradocs/menu', ['subpages' => $menu]); ?>
                        </nav>
                    </section>
                <?php endif; ?>
                <hr />
                <section>
                    <?php if ($plugin->description()->isNotEmpty()): ?>
                        <p><?= $plugin->description() ?></p>
                    <?php endif ?>

                    <div class="tags mt-md">
                        <?php if ($plugin->license()->isNotEmpty()): ?>
                            <span><?= $plugin->license() ?></span>
                        <?php endif ?>

                        <?php if ($plugin->version()->isNotEmpty()): ?>
                            <span><?= $plugin->version() ?></span>
                        <?php endif ?>

                        <?php if ($plugin->homepage()->isNotEmpty()): ?>
                            <span><a href="<?= $plugin->homepage() ?>">Homepage</a></span>
                        <?php endif ?>
                    </div>
                </section>
            <?php endif ?>
        </div>
    </aside>

    <main>
        <?= $slot; ?>
    </main>

</body>

</html>
