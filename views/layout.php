<!doctype html>
<html lang="ru"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title><?= htmlspecialchars($title??'Настольная',ENT_QUOTES,'UTF-8') ?></title><link rel="stylesheet" href="/assets/styles.css"></head>
<body><header class="topbar"><a class="brand" href="/">Настольная</a><nav><a href="/">Главная</a><a href="/catalog">Каталог</a><a href="/cart">Корзина (<?= (int)($cartCount??0) ?>)</a></nav></header>
<main class="container"><?= $content ?></main><footer class="footer">Учебный интернет-магазин · Практическая работа № 9</footer></body></html>
