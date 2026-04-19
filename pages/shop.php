<!-- ══ SHOP PAGE ══ -->
<?php
// Product data array — easy to edit or eventually pull from DB
$products = [
    [
        'id'     => 1,
        'name'   => 'Lechon-Ready Pig',
        'cat'    => 'pigs',
        'icon'   => '🐷',
        'bg'     => '#f1f8e9',
        'badge'  => 'Best Seller',
        'desc'   => '60–80 kg live weight. Ideal for fiesta and events. Healthy, vaccinated, and farm-monitored.',
        'price'  => '₱8,500',
        'unit'   => 'per head',
        'tags'   => ['60–80 kg', 'Vaccinated', 'Farm Raised'],
        'detail' => 'Plump, farm-raised pig ideal for lechon. Healthy, well-fed, and monitored throughout its growth cycle.',
    ],
    [
        'id'     => 2,
        'name'   => 'Feeder Pig (Piglet)',
        'cat'    => 'pigs',
        'icon'   => '🐽',
        'bg'     => '#f9fbe7',
        'badge'  => null,
        'desc'   => '15–20 kg healthy piglet. Great for home fattening or resale. Well-nourished with farm records.',
        'price'  => '₱2,800',
        'unit'   => 'per head',
        'tags'   => ['15–20 kg', 'Feeder', 'Healthy Breed'],
        'detail' => 'Young piglet for home raising. Healthy breed, ideal for backyard farming or resale.',
    ],
    [
        'id'     => 3,
        'name'   => 'Native Goat (Kambing)',
        'cat'    => 'goats',
        'icon'   => '🐐',
        'bg'     => '#fff8e1',
        'badge'  => 'Popular',
        'desc'   => '25–35 kg free-range goat. Grass-fed, firm meat, ideal for caldereta and special dishes.',
        'price'  => '₱4,200',
        'unit'   => 'per head',
        'tags'   => ['25–35 kg', 'Grass-Fed', 'Native'],
        'detail' => 'Free-range, grass-fed native goat. Perfect for caldereta and special occasions.',
    ],
    [
        'id'     => 4,
        'name'   => 'Breeding Goat (Doe)',
        'cat'    => 'goats',
        'icon'   => '🐑',
        'bg'     => '#fce4ec',
        'badge'  => null,
        'desc'   => 'Healthy female goat with complete farm health records. Ready for breeding programs.',
        'price'  => '₱5,500',
        'unit'   => 'per head',
        'tags'   => ['Female', 'Breeding Ready', 'Vaccinated'],
        'detail' => 'Healthy female goat for breeding purposes. Well-managed with complete farm health records.',
    ],
    [
        'id'     => 5,
        'name'   => 'Native Chicken (Manok)',
        'cat'    => 'chickens',
        'icon'   => '🐔',
        'bg'     => '#e8f5e9',
        'badge'  => 'Daily Fresh',
        'desc'   => 'Free-range native chicken, no hormones. Rich flavor — perfect for tinola and adobo.',
        'price'  => '₱320',
        'unit'   => 'per kg',
        'tags'   => ['Free-Range', 'No Hormones', 'Native'],
        'detail' => 'Free-range native chicken with rich, flavorful meat. Ideal for tinola, adobo, and festive meals.',
    ],
    [
        'id'     => 6,
        'name'   => 'Farm Eggs (Itlog)',
        'cat'    => 'chickens',
        'icon'   => '🥚',
        'bg'     => '#fffde7',
        'badge'  => null,
        'desc'   => 'Fresh eggs collected daily from healthy free-range hens. All-natural, no additives.',
        'price'  => '₱8',
        'unit'   => 'per piece',
        'tags'   => ['Daily Fresh', 'Free-Range', 'All-Natural'],
        'detail' => 'Fresh eggs collected daily from free-range hens. No additives, naturally nutritious.',
    ],
];
?>

<div class="page-hero">
    <div class="container">
        <div class="section-label">Our Livestock</div>
        <h1>Farm-Fresh <span style="color:var(--amber)">Products</span></h1>
        <p>All animals are farm-raised with care. Available for purchase and delivery.</p>
    </div>
</div>

<div class="page-content">
    <div class="container">

        <!-- Filter Buttons -->
        <div class="d-flex gap-2 flex-wrap mb-4">
            <button class="filter-btn active" onclick="filterProducts('all', this)">All</button>
            <button class="filter-btn" onclick="filterProducts('pigs', this)">🐷 Pigs</button>
            <button class="filter-btn" onclick="filterProducts('goats', this)">🐐 Goats</button>
            <button class="filter-btn" onclick="filterProducts('chickens', this)">🐔 Chickens</button>
        </div>

        <!-- Product Grid -->
        <div class="row g-4" id="productGrid">
            <?php foreach ($products as $p): ?>
            <div class="col-sm-6 col-lg-4 product-item" data-cat="<?= $p['cat'] ?>">
                <div class="product-card"
                     onclick="openModal(
                         '<?= addslashes($p['name']) ?>',
                         '<?= addslashes($p['detail']) ?>',
                         '<?= $p['icon'] ?>',
                         '<?= $p['price'] ?>',
                         '<?= $p['unit'] ?>',
                         '<?= implode(',', $p['tags']) ?>',
                         <?= $p['id'] ?>
                     )">

                    <!-- Product Image / Icon -->
                    <div class="product-img" style="background:<?= $p['bg'] ?>">
                        <span><?= $p['icon'] ?></span>
                        <?php if ($p['badge']): ?>
                            <span class="product-badge"><?= $p['badge'] ?></span>
                        <?php endif; ?>
                    </div>

                    <!-- Product Body -->
                    <div class="product-body">
                        <h5><?= $p['name'] ?></h5>
                        <p><?= $p['desc'] ?></p>
                        <div class="product-price">
                            <?= $p['price'] ?> <small>/ <?= $p['unit'] ?></small>
                        </div>
                        <div class="product-tags">
                            <?php foreach ($p['tags'] as $tag): ?>
                                <span class="tag"><?= $tag ?></span>
                            <?php endforeach; ?>
                        </div>
                        <div class="product-foot">
                            <span class="avail-text">
                                <span class="avail-dot"></span> Available
                            </span>
                            <button class="btn-order"
                                onclick="event.stopPropagation(); addToCart(<?= $p['id'] ?>, '<?= addslashes($p['name']) ?>', '<?= $p['icon'] ?>', '<?= $p['price'] ?>', '<?= $p['unit'] ?>', this)">
                                <i class="bi bi-cart-plus"></i> Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

    </div>
</div>
