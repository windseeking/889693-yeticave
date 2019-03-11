<div class="container">
    <section class="lots">
        <?php if (isset($lots)): ?>
        <h2>Результаты поиска по запросу «<span><?= $search ;?></span>»</h2>
        <ul class="lots__list">
            <?php foreach ($lots as $lot): ?>
            <li class="lots__item lot">
                <div class="lot__image">
                    <img src="<?= $lot['img_url'] ;?>" width="350" height="260" alt="<?= filter_tags($lot['title']) ;?>">
                </div>
                <div class="lot__info">
                    <span class="lot__category"><?= $lot['cat_name'] ;?></span>
                    <h3 class="lot__title"><a class="text-link" href="lot.php?id=<?= $lot['id'] ;?>"><?= filter_tags($lot['title']) ;?></a></h3>
                    <div class="lot__state">
                        <div class="lot__rate">
                            <span class="lot__amount">Ставок: <?= $lot['bids_amount'] ;?></span>
                            <span class="lot__cost"><?= format_price($lot['current_price'])  ;?></span>
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
        <h2>Ничего не найдено по вашему запросу</h2>
        <?php endif; ?>
    </section>
<!--    <?php /*if (isset($pagination_data)): */?>
    <?/*= include_template('_pagination.php', $pagination_data) ;*/?>
    --><?php /*endif; */?>
</div>
