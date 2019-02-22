<nav class="nav">
    <ul class="nav__list container">
        <li class="nav__item">
            <a href="all-lots.html">Доски и лыжи</a>
        </li>
        <li class="nav__item">
            <a href="all-lots.html">Крепления</a>
        </li>
        <li class="nav__item">
            <a href="all-lots.html">Ботинки</a>
        </li>
        <li class="nav__item">
            <a href="all-lots.html">Одежда</a>
        </li>
        <li class="nav__item">
            <a href="all-lots.html">Инструменты</a>
        </li>
        <li class="nav__item">
            <a href="all-lots.html">Разное</a>
        </li>
    </ul>
</nav>
<form class="form form--add-lot container form--invalid" action="add.php" method="post" enctype="multipart/form-data">
    <!-- form--invalid -->
    <h2>Добавление лота</h2>
    <div class="form__container-two">

        <?php $class = isset($errors['title']) ? 'form__item--invalid' : '';
        $value = isset($lot['title']) ? $lot['title'] : ''; ?>
        <div class="form__item <?= $class; ?>">
            <label for="lot-name">Наименование</label>
            <input id="lot-name" type="text" name="lot[title]" placeholder="Введите наименование лота" value="<?= $value; ?>">
            <?php if (isset($errors['title'])): ?>
                <span class="form__error">Введите наименование лота</span>
            <?php endif; ?>
        </div>

        <?php $class = isset($errors['cat_id']) ? 'form__item--invalid' : '';
        $value = isset($lot['cat_id']) ? $lot['cat_id'] : ''; ?>
        <div class="form__item <?= $class; ?>">
            <label for="category">Категория</label>
            <select id="category" name="lot[cat_id]">
                <option value="0">Выберите категорию</option>
                <?php foreach ($cats as $cat): ?>
                    <option <?= $cat['id'] === $value ? 'selected' : '' ?>
                        value="<?= $cat['id']; ?>"><?= $cat['name']; ?></option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errors['cat_id'])): ?>
                <span class="form__error"><?= $errors['cat_id']; ?></span>
            <?php endif; ?>
        </div>
    </div>

    <?php $class = isset($errors['description']) ? 'form__item--invalid' : '';
    $value = isset($lot['description']) ? $lot['description'] : ''; ?>
    <div class="form__item form__item--wide <?= $class; ?>">
        <label for="message">Описание</label>
        <textarea class="<?= $class; ?>" id="message" name="lot[description]"
                  placeholder="Напишите описание лота"><?= $value; ?></textarea>
        <?php if (isset($errors['description'])): ?>
            <span class="form__error">Напишите описание лота</span>
        <?php endif; ?>
    </div>

    <?php $class = (isset($errors['img_url']) ? 'form__item--invalid' : '');
    $value = isset($_FILES['img_url']) ? $lot['img_url'] : ''; ?>
    <?php if($lot['img_url']): ?>
    <div class="form__item form__item--file form__item--uploaded <?= $class ;?>">
    <?php else: ?>
    <div class="form__item form__item--file <?= $class ;?>">
    <?php endif; ?>
        <label>Изображение</label>
        <div class="preview">
            <button class="preview__remove" type="button">x</button>
            <div class="preview__img">
                <img src="<?= $value; ?>" width="113" height="113" alt="Изображение лота">
            </div>
        </div>
        <div class="form__input-file">
            <input class="visually-hidden" type="file" name="img_url" id="photo2" value="<?= $value; ?>">
            <label for="photo2">
                <span>+ Добавить</span>
            </label>
        </div>
        <?php if (isset($errors['img_url'])): ?>
            <span class="form__error"><?= $errors['img_url']; ?></span>
        <?php endif; ?>
    </div>

    <div class="form__container-three">
        <?php $class = isset($errors['opening_price']) ? 'form__item--invalid' : '';
        $value = isset($lot['opening_price']) ? $lot['opening_price'] : ''; ?>
        <div class="form__item form__item--small <?= $class; ?>">
            <label for="lot-rate">Начальная цена</label>
            <input id="lot-rate" type="number" name="lot[opening_price]" placeholder="0" value="<?= $value; ?>">
            <?php if (isset($errors['opening_price'])): ?>
                <span class="form__error"><?= $errors['opening_price']; ?></span>
            <?php endif; ?>
        </div>

        <?php $class = isset($errors['bid_step']) ? 'form__item--invalid' : '';
        $value = isset($lot['bid_step']) ? $lot['bid_step'] : ''; ?>
        <div class="form__item form__item--small <?= $class; ?>">
            <label for="lot-step">Шаг ставки</label>
            <input id="lot-step" type="number" name="lot[bid_step]" placeholder="0" value="<?= $value; ?>">
            <?php if (isset($errors['bid_step'])): ?>
                <span class="form__error"><?= $errors['bid_step']; ?></span>
            <?php endif; ?>
        </div>

        <?php $class = isset($errors['ends_at']) ? 'form__item--invalid' : '';
        $value = isset($lot['ends_at']) ? $lot['ends_at'] : ''; ?>
        <div class="form__item <?= $class; ?>">
            <label for="lot-date">Дата окончания торгов</label>
            <input class="form__input-date" id="lot-date" type="date" name="lot[ends_at]" value="<?= $value; ?>">
            <?php if (isset($errors['ends_at'])): ?>
                <span class="form__error"><?= $errors['ends_at']; ?></span>
            <?php endif; ?>
        </div>
    </div>
    <?php if (count($errors)): ?>
        <span class="form__error form__error--bottom"><?= $_SESSION['error']; unset($_SESSION['error'])  ;?></span>
    <?php endif; ?>
    <button type="submit" class="button">Добавить лот</button>
</form>
