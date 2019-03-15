<?php $class = count($errors) ? 'form--invalid' : ''; ?>
<form class="form container <?= $class; ?>" action="sign-up.php" method="post" enctype="multipart/form-data">
    <h2>Регистрация нового аккаунта</h2>

    <?php $class = !empty($errors['email']) ? 'form__item--invalid' : '';
    $value = !empty($user['email']) ? $user['email'] : ''; ?>
    <div class="form__item <?= $class; ?>">
        <label for="email">E-mail*</label>
        <input id="email" type="text" name="user[email]" placeholder="Введите e-mail" value="<?= $value; ?>">
        <?php if (!empty($errors['email'])): ?>
            <span class="form__error"><?= $errors['email']; ?></span>
        <?php endif; ?>
    </div>

    <?php $class = !empty($errors['password']) ? 'form__item--invalid' : '';
    $value = !empty($user['password']) ? $user['password'] : ''; ?>
    <div class="form__item <?= $class; ?>">
        <label for="password">Пароль*</label>
        <input id="password" type="password" name="user[password]" placeholder="Введите пароль" value="<?= $value; ?>">
        <?php if (!empty($errors['password'])): ?>
            <span class="form__error">Введите пароль</span>
        <?php endif; ?>
    </div>

    <?php $class = !empty($errors['name']) ? 'form__item--invalid' : '';
    $value = !empty($user['name']) ? $user['name'] : ''; ?>
    <div class="form__item <?= $class; ?>">
        <label for="name">Имя*</label>
        <input id="name" type="text" name="user[name]" placeholder="Введите имя" value="<?= $value; ?>">
        <?php if (!empty($errors['name'])): ?>
            <span class="form__error">Введите имя</span>
        <?php endif; ?>
    </div>

    <?php $class = !empty($errors['contacts']) ? 'form__item--invalid' : '';
    $value = !empty($user['contacts']) ? $user['contacts'] : ''; ?>
    <div class="form__item <?= $class; ?>">
        <label for="message">Контактные данные*</label>
        <textarea id="message" name="user[contacts]"
                  placeholder="Напишите как с вами связаться"><?= $value; ?></textarea>
        <?php if (!empty($errors['contacts'])): ?>
            <span class="form__error">Напишите как с вами связаться</span>
        <?php endif; ?>
    </div>

    <?php $class = !empty($errors['avatar_url']) ? 'form__item--invalid' : '';
    $value = !empty($user['avatar_url']) ? $user['avatar_url'] : ''; ?>

    <?php $class = !empty($errors['avatar_url']) ? 'form__item--invalid' : '';
    $value = !empty($user['avatar_url']) ? $user['avatar_url'] : ''; ?>
    <?php if (!empty($user['avatar_url'])): ?>
    <div class="form__item form__item--file form__item--last form__item--uploaded <?= $class; ?>">
        <?php else: ?>
        <div class="form__item form__item--file form__item--last <?= $class; ?>">
            <?php endif; ?>
            <label>Аватар</label>
            <div class="preview">
                <button class="preview__remove" type="button">x</button>
                <div class="preview__img">
                    <img src="<?= $value; ?>" width="113" height="113" alt="Ваш аватар">
                </div>
            </div>
            <div class="form__input-file">
                <input class="visually-hidden" type="file" id="photo2" name="avatar_url" value=""<?= $value; ?>">
                <label for="photo2">
                    <span>+ Добавить</span>
                </label>
            </div>
            <?php if (!empty($errors['avatar_url'])): ?>
                <span class="form__error"><?= $errors['avatar_url']; ?></span>
            <?php endif; ?>
        </div>
        <?php if (count($errors)): ?>
            <span class="form__error form__error--bottom"><?= $_SESSION['error'];
                unset($_SESSION['error']); ?></span>
        <?php endif; ?>
        <button type="submit" class="button">Зарегистрироваться</button>
        <a class="text-link" href="login.php">Уже есть аккаунт</a>
</form>
