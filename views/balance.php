<div class="page">
    <div style="max-width:720px;margin:0 auto">
        <?php
        /*
         * Ссылка на правила рядом с балансом обязательна: «Огоньки» — это
         * внутренние бонусные единицы, а не деньги, и человек должен видеть
         * условия начисления и списания до того, как что-то потратит.
         */
        ?>
        <p class="hint" style="margin:0 0 20px;line-height:1.6">
            «Огоньки» — внутренние бонусные единицы сайта. Начисление, списание и
            аннулирование описаны в <?= legal_link('loyalty', 'правилах программы лояльности') ?>;
            ограничения за накрутки — в <?= legal_link('blocking', 'правилах блокировки аккаунтов') ?>.
        </p>

        <div class="card balance-card"><div class="card__body">
            <div class="balance-card__top">
                <div>
                    <p class="muted" style="margin:0 0 4px;font-size:13px">Ваш баланс</p>
                    <div class="balance-card__value">
                        <?= icon('flame') ?>
                        <span><?= e(flames_format((float) $me['balance'])) ?></span>
                    </div>
                </div>

                <div class="balance-card__stats">
                    <span class="muted">Начислено за публикаций</span>
                    <strong><?= (int) $earned ?></strong>
                </div>
            </div>

            <ul class="balance-rules">
                <li>
                    <strong><?= e(flames_format((float) setting('flames_per_deal', '3'))) ?></strong>
                    за скидку или промокод, попавшие в ленту
                </li>
                <li>
                    <strong><?= e(flames_format((float) setting('flames_per_blog', '5'))) ?></strong>
                    за запись в блоге — её сложнее написать и она проходит проверку
                </li>
                <li>
                    <strong>+<?= e(flames_format((float) setting('flames_per_vote', '1'))) ?></strong>
                    за каждый голос «горячо» к вашей публикации
                </li>
                <li>
                    <strong>−<?= e(flames_format((float) setting('flames_per_dislike', '0.5'))) ?></strong>
                    за голос «холодно». Баланс при этом не уходит в минус
                </li>
                <li class="muted">
                    Если публикацию удалить, начисленное за неё списывается обратно.
                </li>
            </ul>
        </div></div>

        <div class="card"><div class="card__body">
            <h2 class="card__title">Заявка на вывод</h2>

            <?php if ($errors): ?>
                <div class="alert alert--error"><?php foreach ($errors as $m): ?><div><?= e($m) ?></div><?php endforeach; ?></div>
            <?php endif; ?>

            <?php $min = (float) setting('flames_min_withdraw', '500'); ?>

            <?php if ((float) $me['balance'] < $min): ?>
                <p class="muted" style="margin:0">
                    Минимальная сумма к выводу — <?= e(flames_format($min)) ?> Огоньков.
                    Вам осталось накопить <?= e(flames_format($min - (float) $me['balance'])) ?>.
                </p>
            <?php else: ?>
                <form method="POST" action="<?= e(url('/balans')) ?>">
                    <?= csrf_field() ?>

                    <div class="field">
                        <label for="amount">Сумма, Огоньков</label>
                        <input type="number" step="0.01" id="amount" name="amount" class="input"
                               min="<?= e((string) $min) ?>" max="<?= e((string) round((float) $me['balance'], 2)) ?>"
                               value="<?= e((string) round((float) $me['balance'], 2)) ?>" required>
                        <div class="hint">От <?= e(flames_format($min)) ?> до <?= e(flames_format((float) $me['balance'])) ?>.</div>
                    </div>

                    <div class="field">
                        <label for="full_name">Фамилия, имя, отчество</label>
                        <input type="text" id="full_name" name="full_name" class="input" required>
                    </div>

                    <div class="grid-2">
                        <div class="field">
                            <label for="phone">Телефон</label>
                            <input type="tel" id="phone" name="phone" class="input" placeholder="+7 999 123-45-67" required>
                        </div>

                        <div class="field">
                            <label for="bank">Банк</label>
                            <input type="text" id="bank" name="bank" class="input" placeholder="Т-Банк, Сбер…" required>
                        </div>
                    </div>

                    <div class="field">
                        <label for="requisites">Реквизиты</label>
                        <input type="text" id="requisites" name="requisites" class="input"
                               placeholder="Номер карты или счёта" required>
                    </div>

                    <div class="field">
                        <label for="comment">Комментарий</label>
                        <input type="text" id="comment" name="comment" class="input" placeholder="Не обязательно">
                    </div>

                    <button class="btn btn--primary">Отправить заявку</button>
                </form>

                <p class="hint" style="margin-top:12px">
                    Огоньки списываются сразу при подаче заявки. Если её отклонят —
                    вернутся на баланс.
                </p>
            <?php endif; ?>
        </div></div>

        <?php if ($history): ?>
            <div class="card"><div class="card__body">
                <h2 class="card__title">История заявок</h2>

                <?php foreach ($history as $w): ?>
                    <div class="row" style="padding:10px 0;border-bottom:1px solid var(--border)">
                        <strong><?= e(flames_format((float) $w['amount'])) ?> <?= icon('flame') ?></strong>
                        <span class="spacer"></span>
                        <span class="badge <?= $w['status'] === 'paid' ? 'badge--verified' : ($w['status'] === 'rejected' ? 'badge--expired' : 'badge--pending') ?>">
                            <?= e(withdrawal_status_label($w['status'])) ?>
                        </span>
                        <span class="muted" style="font-size:12px"><?= e(ago($w['created_at'])) ?></span>
                    </div>
                <?php endforeach; ?>
            </div></div>
        <?php endif; ?>
    </div>
</div>
