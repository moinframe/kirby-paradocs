<?php if (!isset($subpages)) $subpages = $page->children() ?>

<ul>
    <?php foreach ($subpages as $p): ?>
        <li class="depth-<?= $p->depth() ?>">
            <?php if ($p->hasChildren()): ?>
                <?php if ($p->depth() > 2): ?>
                    <details <?php e($p->isOpen(), 'open') ?>>
                        <summary>
                            <a<?php e($p->isActive(), ' aria-current') ?> href="<?= $p->url() ?>"><?= $p->title()->html() ?></a>
                        </summary>
                        <?php snippet('paradocs/menu', ['subpages' => $p->children()]) ?>
                    </details>
                <?php else : ?>
                    <a<?php e($p->isActive(), ' aria-current') ?> href="<?= $p->url() ?>"><?= $p->title()->html() ?></a>
                        <?php snippet('paradocs/menu', ['subpages' => $p->children()]) ?>
                    <?php endif; ?>
                <?php else: ?>
                    <a <?php e($p->isActive(), 'aria-current') ?> href="<?= $p->url() ?>"><?= $p->title()->html() ?></a>
                <?php endif ?>
        </li>
    <?php endforeach ?>
</ul>