<?php snippet('paradocs/layout', slots: true); ?>

<?php if ($page->text()->isNotEmpty()): ?>
    <article class="text">
        <?= $page->text()->kt() ?>
    </article>
<?php endif ?>

<?php endsnippet(); ?>
