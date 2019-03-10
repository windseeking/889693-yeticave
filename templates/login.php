<?php $class = count($errors) ? 'form--invalid' : '' ;?>
<form class="form container <?= $class ;?>" method="post" enctype="multipart/form-data">
    <h2>Вход</h2>

    <?php $class = isset($errors['email']) ? 'form__item--invalid' : '';
    $value = isset($form['email']) ? $form['email'] : ''; ?>
    <div class="form__item <?= $class; ?>">
        <label for="email">E-mail*</label>
        <input id="email" type="text" name="email" placeholder="Введите e-mail" value="<?= $value ;?>">
        <?php if (isset($errors['email'])): ?>
            <span class="form__error"><?= $errors['email'] ;?></span>
        <?php endif; ?>
    </div>

    <?php $class = isset($errors['password']) ? 'form__item--invalid' : '';
    $value = isset($form['password']) ? $form['password'] : ''; ?>
    <div class="form__item form__item--last <?= $class; ?>">
        <label for="password">Пароль*</label>
        <input id="password" type="password" name="password" placeholder="Введите пароль" value="<?= $value ;?>">
        <?php if (isset($errors['password'])): ?>
            <span class="form__error"><?= $errors['password'] ;?></span>
        <?php endif; ?>
    </div>
    <button type="submit" class="button">Войти</button>
</form>
