<section class="lot-item container">
    <?php if (isset($lot)): ?>
        <h2><?= filter_tags($lot['title']); ?></h2>
        <div class="lot-item__content">
            <div class="lot-item__left">
                <div class="lot-item__image">
                    <img src="<?= $lot['img_url']; ?>" width="730" height="548"
                         alt="<?= filter_tags($lot['title']); ?>">
                </div>
                <p class="lot-item__category">Категория: <span><?= $lot['cat_name']; ?></span></p>
                <p class="lot-item__description"><?= filter_tags($lot['description']); ?></p>
            </div>
            <div class="lot-item__right">
                <div class="lot-item__state">
                    <?php if (!is_time_elapsed($lot['ends_at'])): ?>
                        <div class="lot-item__timer timer">
                            <?= time_left($lot['ends_at']); ?>
                        </div>
                    <?php else: ?>
                        Торги окончены
                    <?php endif; ?>

                    <div class="lot-item__cost-state">
                        <div class="lot-item__rate">
                            <?php if (!is_time_elapsed($lot['ends_at'])): ?>
                                <span class="lot-item__amount">Текущая цена</span>
                            <?php else: ?>
                                <span class="lot-item__amount">Окончательная цена</span>
                            <?php endif; ?>
                            <span class="lot-item__cost"><?= format_price($lot['current_price']); ?></span>
                        </div>
                        <?php if (!is_time_elapsed($lot['ends_at'])): ?>
                            <div class="lot-item__min-cost">
                                Мин. ставка <span><?= format_price($lot['current_price'] + $lot['bid_step']); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if (isset($_SESSION['user']) and is_bid_block_shown($user_id, $lot['author_id'],
                            $bids) and !is_time_elapsed($lot['ends_at'])): ?>
                        <form class="lot-item__form" method="post" enctype="multipart/form-data">
                            <?php $class = isset($errors['bid']) ? 'form__item--invalid' : '';
                            $value = isset($form['bid']) ? $form['bid'] : ''; ?>
                            <p class="lot-item__form-item form__item <?= $class; ?>">
                                <label for="cost">Ваша ставка</label>
                                <input id="cost" type="text" name="bid"
                                       placeholder="<?= $lot['current_price'] + $lot['bid_step']; ?>"
                                       value="<?= $value; ?>">
                                <?php if (isset($errors['bid'])): ?>
                                    <span class="form__error"><?= $errors['bid']; ?></span>
                                <?php endif; ?>
                            </p>
                            <button type="submit" class="button">Сделать ставку</button>
                        </form>
                    <?php endif; ?>
                </div>

                <?php if (isset($bids)): ?>
                    <div class="history">
                        <h3>История ставок (<span><?= count($bids); ?></span>)</h3>
                        <table class="history__list">
                            <?php foreach ($bids as $bid): ?>
                                <tr class="history__item">
                                    <td class="history__name"><?= filter_tags($bid['user_name']); ?></td>
                                    <td class="history__price"><?= format_price($bid['buyer_price']); ?></td>
                                    <td class="history__time"><?= time_passed($bid['created_at']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</section>
