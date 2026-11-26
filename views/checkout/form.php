<section class="page-heading"><div class="eyebrow">ПОСЛЕДНИЙ ШАГ</div><h1>Оформление заказа</h1></section>
<?php if($empty): ?><section class="panel"><p>Сначала добавьте товары в корзину.</p><a class="button" href="/catalog">В каталог</a></section>
<?php else: ?>
<section class="checkout-grid"><form class="panel stack-form" method="post" action="/checkout"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken,ENT_QUOTES,'UTF-8') ?>">
<?php foreach($errors as $error): ?><div class="alert alert-error"><?= htmlspecialchars($error,ENT_QUOTES,'UTF-8') ?></div><?php endforeach; ?>
<label for="name">Имя</label><input id="name" name="name" required value="<?= htmlspecialchars($old['name']??'',ENT_QUOTES,'UTF-8') ?>">
<label for="email">Email</label><input id="email" name="email" type="email" required value="<?= htmlspecialchars($old['email']??'',ENT_QUOTES,'UTF-8') ?>">
<label for="address">Адрес или пункт получения</label><input id="address" name="address" required value="<?= htmlspecialchars($old['address']??'',ENT_QUOTES,'UTF-8') ?>">
<label for="delivery">Способ доставки</label><select id="delivery" name="delivery" required><?php foreach($deliveryMethods as $method): ?><option value="<?= htmlspecialchars($method['id'],ENT_QUOTES,'UTF-8') ?>" <?= ($old['delivery']??'')===$method['id']?'selected':'' ?>><?= htmlspecialchars($method['name'],ENT_QUOTES,'UTF-8') ?> — <?= (int)$method['price'] ?> ₽</option><?php endforeach; ?></select>
<button class="button">Подтвердить заказ</button></form>
<aside class="panel"><h2>Ваш заказ</h2><?php foreach($lines as $line): ?><p><?= htmlspecialchars($line['product']->name,ENT_QUOTES,'UTF-8') ?> × <?= $line['quantity'] ?></p><?php endforeach; ?><p>Товары: <?= number_format($total,0,',',' ') ?> ₽</p></aside></section>
<?php endif; ?>
