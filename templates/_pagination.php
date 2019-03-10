<ul class="pagination-list">
    <li class="pagination-item pagination-item-prev"><a>Назад</a></li>
    <?php foreach ($pages as $page): ?>
        <li class="pagination-item <?php if ($page == $cur_page): ?> pagination-item-active<?php endif; ?>">
            <a href="<?= $url; ?>"><?= $page; ?></a></li>
    <?php endforeach; ?>
    <li class="pagination-item pagination-item-next"><a href="#">Вперед</a></li>
</ul>
