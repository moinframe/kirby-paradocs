<?php snippet('paradocs/layout', slots: true); ?>
<article class="text">
    <h1><?= $page->title() ?></h1>

    <?php if ($page->text()->isNotEmpty()): ?>
        <?= $page->text()->kt() ?>
    <?php endif ?>
</article>

<nav class="text">
    <ul>
        <?php foreach ($page->children() as $plugin): ?>
            <li>
                <a href="<?= $plugin->url() ?>">
                    <?= $plugin->title() ?>
                </a>
            </li>
        <?php endforeach ?>
    </ul>
</nav>
<?php endsnippet(); ?>
