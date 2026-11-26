<section class="hero">
    <div class="eyebrow">МАГАЗИН НАСТОЛЬНЫХ ИГР</div>
    <h1><?= htmlspecialchars($shop_name ?? 'Настольная', ENT_QUOTES, 'UTF-8') ?></h1>
    <p><?= htmlspecialchars($description ?? '', ENT_QUOTES, 'UTF-8') ?></p>
    <a class="button" href="/catalog">Перейти в каталог</a>
</section>
<section class="panel">
    <h2>Почему выбирают нас</h2>
    <div class="benefits">
        <?php foreach (($benefits ?? []) as $benefit): ?>
            <article class="benefit"><?= htmlspecialchars((string) $benefit, ENT_QUOTES, 'UTF-8') ?></article>
        <?php endforeach; ?>
    </div>
</section>
