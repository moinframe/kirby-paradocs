<?php snippet('paradocs/layout', slots: true); ?>

<article class="text">
    <h1><?= $page->title() ?></h1>

    <?php if ($page->text()->isNotEmpty()): ?>
        <?= $page->text() ?>
    <?php endif ?>

</article>
<?php endsnippet(); ?>