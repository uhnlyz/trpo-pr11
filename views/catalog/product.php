<section class="hero">
<div class="eyebrow"><?= htmlspecialchars($product->categoryName,ENT_QUOTES,'UTF-8') ?></div>
<h1><?= htmlspecialchars($product->name,ENT_QUOTES,'UTF-8') ?></h1>
<p><?= htmlspecialchars($product->description,ENT_QUOTES,'UTF-8') ?></p>
<p><strong><?= $product->formattedPrice() ?></strong></p>
<p>В наличии: <?= $product->stock ?> шт.</p>
<a class="button" href="/catalog">Вернуться в каталог</a>
</section>
