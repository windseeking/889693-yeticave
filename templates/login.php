<?php $class = count($errors) ? 'form--invalid' : ''; ?>
<form class="form container <?= $class; ?>" method="post" enctype="multipart/form-data">
    <h2>Вход</h2>

    <?php $class = !empty($errors['email']) ? 'form__item--invalid' : '';
    $value = !empty($form['email']) ? $form['email'] : ''; ?>
    <div class="form__item <?= $class; ?>">
        <label for="email">E-mail*</label>
        <input id="email" type="email" name="email" placeholder="Введите e-mail" value="<?= $value; ?>">
        <?php if (!empty($errors['email'])): ?>
            <span class="form__error"><?= $errors['email']; ?></span>
        <?php endif; ?>
    </div>

    <?php $class = !empty($errors['password']) ? 'form__item--invalid' : '';
    $value = !empty($form['password']) ? $form['password'] : ''; ?>
    <div class="form__item form__item--last <?= $class; ?>">
        <label for="password">Пароль*</label>
        <input id="password" type="password" name="password" placeholder="Введите пароль" value="<?= $value; ?>">
        <?php if (!empty($errors['password'])): ?>
            <span class="form__error"><?= $errors['password']; ?></span>
        <?php endif; ?>
    </div>
    <button type="submit" class="button">Войти</button>
</form>
