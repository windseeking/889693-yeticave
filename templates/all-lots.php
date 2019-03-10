<div class="container">
    <section class="lots">
        <?php if (!empty($lots)): ?>
        <h2>Все лоты в категории <span>«<?= $cat_name; ?>»</span></h2>
        <ul class="lots__list">
            <?php foreach ($lots as $lot): ?>
            <li class="lots__item lot">
                <div class="lot__image">
                    <img src="<?= $lot['img_url'] ;?>" width="350" height="260" alt="<?= $lot['title'] ;?>">
                </div>
                <div class="lot__info">
                    <span class="lot__category"><?= $lot['cat_name'] ;?></span>
                    <h3 class="lot__title"><a class="text-link" href="/lot.php?id=<?= $lot['id'] ;?>"><?= $lot['title'] ;?></a></h3>
                    <div class="lot__state">
                        <div class="lot__rate">
                            <span class="lot__amount">Ставок: <?= $lot['bids_amount'] ;?></span>
                            <span class="lot__cost"><?= format_price($lot['current_price']) ;?></span>
                        </div>
                        <div class="lot__timer timer">
                            <?= time_left($lot['ends_at']) ;?>
                        </div>
                    </div>
                </div>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php else: ?>
        <h2>В этой категории пока нет лотов</h2>
        <?php endif; ?>
    </section>
<!--    --><?//= include_template('_pagination.php', $pagination_data) ;?>
</div>