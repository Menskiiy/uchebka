<div class="layout layout--aside-lg">
    <aside class="card"><div class="card__body">
        <h3 class="card__title">Что публикуем</h3>
        <div class="stack" style="gap:8px">
            <?php foreach (publishable_types() as $key => $typeMeta): ?>
                <a href="<?= e(url('/dobavit/' . $typeMeta['slug'])) ?>"
                   class="btn <?= $key === $type ? 'btn--primary' : 'btn--ghost' ?> btn--block"
                   style="justify-content:flex-start"><?= e($typeMeta['label']) ?></a>
            <?php endforeach; ?>
        </div>
    </div></aside>

    <div class="card"><div class="card__body">
        <h1 style="font-size:21px;margin-top:0">Добавить: <?= e(type_label($type)) ?></h1>

        <?php if ($errors): ?>
            <div class="alert alert--error">
                <?php foreach ($errors as $message): ?><div><?= e($message) ?></div><?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= e(url('/dobavit/' . cfg('thread_types')[$type]['slug'])) ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <?php if (is_commercial($type)): ?>
                <div class="field">
                    <label for="url">Ссылка на предложение <span class="muted">(не обязательно)</span></label>
                    <input type="url" id="url" name="url" class="input" value="<?= e($old['url'] ?? '') ?>" placeholder="https://example.com/tovar">
                    <div class="hint">Партнёрская ссылка тоже подойдёт — магазин укажите ниже вручную.</div>
                </div>

                <div class="field">
                    <label for="merchant_name">Магазин</label>
                    <input type="text" id="merchant_name" name="merchant_name" class="input"
                           list="merchants-list" autocomplete="off"
                           value="<?= e($old['merchant_name'] ?? '') ?>"
                           placeholder="Начните вводить название">
                    <datalist id="merchants-list">
                        <?php foreach ($merchants as $m): ?>
                            <option value="<?= e($m['name']) ?>"></option>
                        <?php endforeach; ?>
                    </datalist>
                    <div class="hint">
                        Оставьте пустым — определим по ссылке. Для партнёрских ссылок
                        выберите магазин из подсказок.
                    </div>
                </div>
            <?php endif; ?>

            <div class="field">
                <label for="title">Заголовок</label>
                <input type="text" id="title" name="title" class="input" value="<?= e($old['title'] ?? '') ?>"
                       placeholder="Краткий и ёмкий заголовок..." required>
            </div>

            <?php if ($type === 'voucher'): ?>
                <div class="field">
                    <label for="code">Промокод</label>
                    <input type="text" id="code" name="code" class="input" value="<?= e($old['code'] ?? '') ?>"
                           placeholder="Введите промокод" data-code-check required>
                    <div class="code-check" data-code-check-result></div>
                </div>
            <?php endif; ?>

            <?php if ($type === 'discussion'): ?>
                <div class="field">
                    <label for="group_id">Раздел</label>
                    <select name="group_id" id="group_id" class="input" required>
                        <option value="">— выберите раздел —</option>
                        <?php foreach ($groups as $g): ?>
                            <option value="<?= (int) $g['id'] ?>" <?= (($old['group_id'] ?? '') == $g['id']) ? 'selected' : '' ?>><?= e($g['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>

            <div class="field">
                <label>Описание</label>
                <?php
                // в блоге нужны заголовки и картинки, в остальном хватает
                // выделения — меньше шансов испортить вёрстку
                $name        = 'body';
                $value       = $old['body'] ?? '';
                // картинки нужны везде: к скидке часто прикладывают скриншот
                // корзины или чека — без него условия приходится объяснять словами
                $tools       = $type === 'blog'
                    ? 'h2 heading bold italic underline strike list numlist quote link unlink image emoji clear undo redo'
                    : 'bold italic underline strike list link image emoji clear undo';
                $rows        = $type === 'blog' ? 12 : 7;
                $placeholder = $type === 'blog'
                    ? 'Расскажите, как вы находите выгодные предложения…'
                    : 'Опишите предложение своими словами и объясните, почему это выгодно…';
                require __DIR__ . '/partials/editor.php';
                ?>
            </div>

            <?php // картинка нужна и скидке с промокодом, и блогу — только там,
                  // где текст один сплошной, изображение необязательно ?>
            <?php if (is_commercial($type) || $type === 'blog'): ?>
                <div class="field">
                    <label>Изображение</label>
                    <input type="url" name="image_url" class="input" value="<?= e($old['image_url'] ?? '') ?>" placeholder="Вставьте URL изображения">
                    <div class="hint">или загрузите файл:</div>
                    <input type="file" name="image" accept="image/*" style="margin-top:8px"
                           data-max-size="<?= upload_limit_bytes() ?>">
                </div>

                <div class="field">
                    <label>Ещё снимки</label>
                    <input type="file" name="images[]" accept="image/*" multiple
                           data-max-size="<?= upload_limit_bytes() ?>">
                    <div class="hint">
                        До восьми штук: условия акции, чек, товар с других сторон. Их можно будет
                        листать на странице предложения. Первое изображение выше остаётся обложкой —
                        именно оно показывается в ленте и при отправке ссылки в мессенджер.
                    </div>
                </div>
            <?php endif; ?>

            <?php if (is_commercial($type)): ?>
                <?php if ($type === 'deal'): ?>
                <div class="grid-2">
                    <div class="field">
                        <label for="price">Цена, ₽</label>
                        <input type="number" step="0.01" id="price" name="price" class="input" value="<?= e($old['price'] ?? '') ?>" placeholder="0">
                    </div>
                    <div class="field">
                        <label for="old_price">Цена в других магазинах, ₽</label>
                        <input type="number" step="0.01" id="old_price" name="old_price" class="input" value="<?= e($old['old_price'] ?? '') ?>" placeholder="0">
                    </div>
                    <div class="field">
                        <label for="discount_percent">Процент скидки</label>
                        <input type="number" id="discount_percent" name="discount_percent" class="input" value="<?= e($old['discount_percent'] ?? '') ?>" placeholder="0">
                    </div>
                </div>
                <?php endif; ?>

                <div class="field">
                    <label for="expires_at">Действует до</label>
                    <input type="date" id="expires_at" name="expires_at" class="input"
                           value="<?= e($old['expires_at'] ?? date('Y-m-d', time() + (int) cfg('lifetime_days') * 86400)) ?>"
                           min="<?= e(date('Y-m-d')) ?>" max="<?= e(date('Y-m-d', time() + 730 * 86400)) ?>">

                    <div class="date-presets">
                        <button type="button" class="btn btn--sm" data-days="1" data-target="expires_at">1 день</button>
                        <button type="button" class="btn btn--sm" data-days="3" data-target="expires_at">3 дня</button>
                        <button type="button" class="btn btn--sm" data-days="7" data-target="expires_at">Неделя</button>
                        <button type="button" class="btn btn--sm" data-days="30" data-target="expires_at">Месяц</button>
                        <button type="button" class="btn btn--sm" data-days="" data-target="expires_at">Без срока</button>
                    </div>

                    <div class="hint">
                        Срок попадает во вкладку «Сгорает» и в рассылку — с ним находку увидит
                        больше людей. После этой даты предложение уйдёт из ленты и будет помечено
                        как истёкшее. Если срок не объявлен, нажмите «Без срока».
                    </div>
                </div>

                <div class="field">
                    <label>Категории</label>
                    <?php $selected = array_map('intval', (array) ($old['categories'] ?? [])); ?>
                    <?php require __DIR__ . '/partials/categories.php'; ?>
                    <div class="hint">Отметьте подходящие — по ним предложение найдут в каталоге.</div>
                </div>
            <?php endif; ?>


            <?php if (is_moderator()): ?>
                <div class="field">
                    <label for="ad_marking">Маркировка рекламы</label>
                    <input type="text" id="ad_marking" name="ad_marking" class="input"
                           value="<?= e($old['ad_marking'] ?? '') ?>"
                           placeholder="«Реклама», ООО «Яндекс», ИНН 7736207543, erid: ...">
                    <div class="hint">
                        Видно только модераторам. Текст выводится мелким шрифтом
                        внизу публикации, не мешая описанию.
                    </div>
                </div>
            <?php endif; ?>
<?php
            /*
             * Отметки перед публикацией: за содержание отвечает автор, и это
             * должно быть сказано до отправки, а не в правилах, которые никто
             * не открывал. Вторая отметка — про рекламный характер: закон
             * о рекламе требует маркировки, и решать это на глаз нельзя.
             */
            ?>
            <div class="consents" style="margin-top:20px">
                <label class="check">
                    <input type="checkbox" name="confirm_accuracy" value="1" required>
                    <span>
                        Подтверждаю, что размещаемые сведения достоверны, актуальны, не нарушают
                        права третьих лиц и размещаются мной на законном основании — в соответствии с
                        <?= legal_link('content', 'правилами размещения публикаций') ?>.
                        Чужие тексты, изображения и персональные данные других людей публиковать нельзя.
                    </span>
                </label>

                <label class="check">
                    <input type="checkbox" name="confirm_not_ad" value="1" required>
                    <span>
                        Подтверждаю, что материал не является рекламой, требующей предварительного
                        согласования и маркировки, либо обязуюсь предоставить администрации сведения
                        для проверки его рекламного статуса.
                    </span>
                </label>
            </div>

            <div class="row" style="margin-top:20px">
                <a href="<?= e(url('/')) ?>" class="btn btn--ghost">Отменить</a>
                <button class="btn btn--primary">Опубликовать</button>
            </div>
        </form>
    </div></div>
</div>
