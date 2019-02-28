<nav class="nav">
    <ul class="nav__list container">
        <?php foreach ($cats as $cat): ?>
            <li class="nav__item">
                <a href="#"><?= $cat['name']; ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
<div class="container">
    <section class="lots">
        <h2>Результаты поиска по запросу «<span><?= $search ;?></span>»</h2>
        <ul class="lots__list">
            <?php foreach ($lots as $lot): ?>
            <li class="lots__item lot">
                <div class="lot__image">
                    <img src="<?= $lot['img_url'] ;?>" width="350" height="260" alt="<?= $lot['title'] ;?>">
                </div>
                <div class="lot__info">
                    <span class="lot__category"><?= $lot['cat_name'] ;?></span>
                    <h3 class="lot__title"><a class="text-link" href="lot.php?id=<?= $lot['id'] ;?>"><?= $lot['title'] ;?></a></h3>
                    <div class="lot__state">
                        <div class="lot__rate">
                            <span class="lot__amount"><?= $lot['bid_amount'] ;?></span>
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
    </section>
    <?= include_template('_pagination.php', $pagination_data) ;?>
</div>
