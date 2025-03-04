<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= Moinframe\ParaDocs\Options::title() ?></title>
    <?= css(Kirby\Cms\App::plugin('moinframe/kirby-paradocs')->asset('index.css')) ?>
</head>

<body data-template="<?= $page->intendedTemplate()->name() ?>">
    <aside>
        <div class="sidebar">
            <?php if (isset($plugin)): ?>
                <header>
                    <p><a href="<?= $plugin->url() ?>"><strong><?= $plugin->title() ?></strong></a></p>
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