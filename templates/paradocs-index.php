<?php snippet('paradocs/layout', slots: true); ?>
<article class="text">
    <h1><?= $page->title() ?></h1>

    <?php if ($page->text()->isNotEmpty()): ?>
        <?= $page->text()  ?>
    <?php endif ?>
</article>

<nav class="text">
    <ul class="cards">
        <?php foreach ($page->children() as $plugin): ?>
            <li>
                <section class="card">
                    <header>
                        <p><a href="<?= $plugin->url() ?>"><?= $plugin->title() ?></a></p>
                    </header>

                    <?php if ($plugin->description()->isNotEmpty()): ?>
                        <p><?= $plugin->description() ?></p>
                    <?php endif ?>
                    <footer>
                        <div class="tags mt-md">
                            <?php if ($plugin->license()->isNotEmpty()): ?>
                                <span><?= $plugin->license() ?></span>
                            <?php endif ?>

                            <?php if ($plugin->content()->version()->isNotEmpty()): ?>
                                <span><?= $plugin->content()->version() ?></span>
                            <?php endif ?>

                            <?php if ($plugin->homepage()->isNotEmpty()): ?>
                                <span><a href="<?= $plugin->homepage() ?>">Homepage</a></span>
                            <?php endif ?>
                        </div>
                    </footer>
                </section>
            </li>
        <?php endforeach ?>
    </ul>
</nav>
<?php endsnippet(); ?>
