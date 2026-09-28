<div class="layout">
    <div>
        <h1 style="font-size:22px"><?= e($heading) ?></h1>

        <?php if ($kind === 'categories'): ?>
            <div class="grid-2">
                <?php foreach ($items as $parent): ?>
                    <div class="card"><div class="card__body">
                        <h3 class="card__title">
                            <a href="<?= e(url('/kategoriya/' . rawurlencode($parent['slug']))) ?>"><?= e($parent['name']) ?></a>
                        </h3>
                        <div class="checks">
                            <?php foreach ($parent['children'] as $child): ?>
                                <a href="<?= e(url('/kategoriya/' . rawurlencode($child['slug']))) ?>" class="badge"><?= e($child['name']) ?></a>
                            <?php endforeach; ?>
                        </div>
                    </div></div>
                <?php endforeach; ?>
            </div>

        <?php elseif ($kind === 'merchants'): ?>
            <div class="card"><div class="card__body">
                <?php foreach ($items as $m): ?>
                    <div class="row" style="padding:10px 0;border-bottom:1px solid var(--border)">
                        <span class="merchant-chip" style="background:<?= e($m['logo_color']) ?>;width:38px;height:38px;font-size:10px">
                            <?= e(mb_substr($m['name'], 0, 7)) ?>
                        </span>
                        <a href="<?= e(url('/magazin/' . rawurlencode($m['slug']))) ?>" style="font-weight:600"><?= e($m['name']) ?></a>
                        <span class="spacer"></span>
                        <span class="muted"><?= (int) $m['threads_count'] ?> публикаций</span>
                    </div>
                <?php endforeach; ?>
            </div></div>

        <?php else: ?>
            <div class="grid-2">
                <?php foreach ($items as $g): ?>
                    <div class="card"><div class="card__body">
                        <h3 class="card__title"><a href="<?= e(url('/obsuzhdeniya/' . rawurlencode($g['slug']))) ?>"><?= e($g['name']) ?></a></h3>
                        <p class="muted" style="margin:0;font-size:13px"><?= e($g['description']) ?></p>
                        <p class="muted" style="margin:8px 0 0;font-size:13px"><?= (int) $g['threads_count'] ?> тем</p>
                    </div></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php require __DIR__ . '/partials/sidebar.php'; ?>
</div>
