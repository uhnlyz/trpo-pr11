<section class="page-heading"><div class="eyebrow">ВАШ ЗАКАЗ</div><h1>Корзина</h1></section><section class="panel">
<?php if($lines===[]): ?><p>Корзина пока пуста.</p><a class="button" href="/catalog">Перейти в каталог</a>
<?php else: ?><?php foreach($lines as $line): ?><article class="cart-line"><div><strong><?= htmlspecialchars($line['product']->name,ENT_QUOTES,'UTF-8') ?></strong><small><?= $line['product']->formattedPrice() ?> за шт.</small></div>
<form method="post" action="/cart/update"><input type="hidden" name="product_id" value="<?= $line['product']->id ?>"><label>Количество <input type="number" name="quantity" min="1" value="<?= $line['quantity'] ?>"></label><button class="button button-secondary">Обновить</button></form>
<strong><?= number_format($line['subtotal'],0,',',' ') ?> ₽</strong><form method="post" action="/cart/remove"><input type="hidden" name="product_id" value="<?= $line['product']->id ?>"><button class="button button-ghost">Удалить</button></form></article><?php endforeach; ?>
<div class="cart-total">Итого: <strong><?= number_format($total,0,',',' ') ?> ₽</strong></div><a class="button" href="/checkout">Оформить заказ</a><?php endif; ?></section>
