<section class="promo">
    <h2 class="promo__title">Нужен стафф для катки?</h2>
    <p class="promo__text">На нашем интернет-аукционе ты найдёшь самое эксклюзивное сноубордическое и
        горнолыжное снаряжение.</p>
    <?php if (!empty($cats)): ?>
        <ul class="promo__list">
            <?php foreach ($cats as $cat): ?>
                <li class="promo__item promo__item--boards">
                    <a class="promo__link"
                       href="all-lots.php?cat=<?= $cat['id']; ?>"><?= filter_tags($cat['name']); ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</section>
<section class="lots">
    <div class="lots__header">
        <h2>Открытые лоты</h2>
    </div>
    <?php if (!empty($lots)): ?>
        <ul class="lots__list">
            <?php foreach ($lots as $lot): ?>
                <li class="lots__item lot">
                    <div class="lot__image">
                        <img src="<?= $lot['img_url']; ?>" width="350" height="260"
                             alt="<?= filter_tags($lot['title']); ?>">
                    </div>
                    <div class="lot__info">
                        <span class="lot__category"><?= $lot['cat_name']; ?></span>
                        <h3 class="lot__title"><a class="text-link"
                                                  href="lot.php?id=<?= $lot['id']; ?>"><?= filter_tags($lot['title']); ?></a>
                        </h3>
                        <div class="lot__state">
                            <div class="lot__rate">
                                <span class="lot__amount">Ставок: <?= $lot['bids_amount']; ?></span>
                                <span class="lot__cost"><?= format_price($lot['current_price']); ?></span>
                            </div>
                            <div class="lot__timer timer">
                                <?= time_left($lot['ends_at']); ?>
                            </div>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</section>
