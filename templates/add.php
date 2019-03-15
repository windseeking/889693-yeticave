<?php $class = count($errors) ? 'form--invalid' : ''; ?>
<form class="form form--add-lot container <?= $class; ?>" action="add.php" method="post" enctype="multipart/form-data">
    <h2>Добавление лота</h2>
    <div class="form__container-two">

        <?php $class = !empty($errors['title']) ? 'form__item--invalid' : '';
        $value = !empty($lot['title']) ? $lot['title'] : ''; ?>
        <div class="form__item <?= $class; ?>">
            <label for="lot-name">Наименование</label>
            <input id="lot-name" type="text" name="lot[title]" placeholder="Введите наименование лота"
                   value="<?= $value; ?>">
            <?php if (!empty($errors['title'])): ?>
                <span class="form__error">Введите наименование лота</span>
            <?php endif; ?>
        </div>

        <?php $class = !empty($errors['cat_id']) ? 'form__item--invalid' : '';
        $value = !empty($lot['cat_id']) ? $lot['cat_id'] : ''; ?>
        <div class="form__item <?= $class; ?>">
            <label for="category">Категория</label>
            <select id="category" name="lot[cat_id]">
                <option value="0">Выберите категорию</option>
                <?php foreach ($cats as $cat): ?>
                    <option <?= $cat['id'] === $value ? 'selected' : '' ?>
                        value="<?= $cat['id']; ?>"><?= $cat['name']; ?></option>
                <?php endforeach; ?>
            </select>
            <?php if (!empty($errors['cat_id'])): ?>
                <span class="form__error"><?= $errors['cat_id']; ?></span>
            <?php endif; ?>
        </div>
    </div>

    <?php $class = !empty($errors['description']) ? 'form__item--invalid' : '';
    $value = !empty($lot['description']) ? $lot['description'] : ''; ?>
    <div class="form__item form__item--wide <?= $class; ?>">
        <label for="message">Описание</label>
        <textarea class="<?= $class; ?>" id="message" name="lot[description]"
                  placeholder="Напишите описание лота"><?= $value; ?></textarea>
        <?php if (!empty($errors['description'])): ?>
            <span class="form__error">Напишите описание лота</span>
        <?php endif; ?>
    </div>

    <?php $class = (!empty($errors['img_url']) ? 'form__item--invalid' : '');
    $value = !empty($lot['img_url']) ? $lot['img_url'] : ''; ?>
    <?php if (!empty($lot['img_url'])): ?>
    <div class="form__item form__item--file form__item--uploaded <?= $class; ?>">
        <?php else: ?>
        <div class="form__item form__item--file <?= $class; ?>">
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
            <?php if (!empty($errors['img_url'])): ?>
                <span class="form__error"><?= $errors['img_url']; ?></span>
            <?php endif; ?>
        </div>

        <div class="form__container-three">
            <?php $class = !empty($errors['opening_price']) ? 'form__item--invalid' : '';
            $value = !empty($lot['opening_price']) ? $lot['opening_price'] : ''; ?>
            <div class="form__item form__item--small <?= $class; ?>">
                <label for="lot-rate">Начальная цена</label>
                <input id="lot-rate" type="number" name="lot[opening_price]" placeholder="0" value="<?= $value; ?>">
                <?php if (!empty($errors['opening_price'])): ?>
                    <span class="form__error"><?= $errors['opening_price']; ?></span>
                <?php endif; ?>
            </div>

            <?php $class = !empty($errors['bid_step']) ? 'form__item--invalid' : '';
            $value = !empty($lot['bid_step']) ? $lot['bid_step'] : ''; ?>
            <div class="form__item form__item--small <?= $class; ?>">
                <label for="lot-step">Шаг ставки</label>
                <input id="lot-step" type="number" name="lot[bid_step]" placeholder="0" value="<?= $value; ?>">
                <?php if (!empty($errors['bid_step'])): ?>
                    <span class="form__error"><?= $errors['bid_step']; ?></span>
                <?php endif; ?>
            </div>

            <?php $class = !empty($errors['ends_at']) ? 'form__item--invalid' : '';
            $value = !empty($lot['ends_at']) ? $lot['ends_at'] : ''; ?>
            <div class="form__item <?= $class; ?>">
                <label for="lot-date">Дата окончания торгов</label>
                <input class="form__input-date" id="lot-date" type="date" name="lot[ends_at]" value="<?= $value; ?>">
                <?php if (!empty($errors['ends_at'])): ?>
                    <span class="form__error"><?= $errors['ends_at']; ?></span>
                <?php endif; ?>
            </div>
        </div>
        <?php if (count($errors) || !empty($_SESSION['error'])): ?>
            <span class="form__error form__error--bottom"><?= $_SESSION['error'];
                unset($_SESSION['error']); ?></span>
        <?php endif; ?>
        <button type="submit" class="button">Добавить лот</button>
</form>
