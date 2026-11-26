<section class="hero"><div class="eyebrow">ЗАКАЗ СОЗДАН</div>
<?php if($order===null): ?><h1>Заказ не найден</h1><a class="button" href="/">На главную</a>
<?php else: ?><h1>Спасибо за заказ №<?= (int)$order['id'] ?>!</h1><p>Сумма заказа: <strong><?= number_format((int)$order['total'],0,',',' ') ?> ₽</strong></p><p>Способ доставки: <?= htmlspecialchars((string)$order['delivery_id'],ENT_QUOTES,'UTF-8') ?></p><a class="button" href="/catalog">Вернуться в каталог</a><?php endif; ?></section>
