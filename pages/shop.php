<!-- ══ SHOP PAGE ══ -->
<?php
include_once './config/pdo_connection.php';
include_once './includes/auth.php';

$isLoggedIn = isset($_SESSION['user']);

$user = currentUser();

// ── Instantiate your Connect class and grab the PDO connection ───────────────
$db  = new Connect();
$pdo = $db->connection;

// ── Category meta (icon, background colour, price unit) ──────────────────────
$catMeta = [
    'pigs'     => ['icon' => '🐷', 'bg' => '#f1f8e9', 'unit' => 'per head'],
    'goats'    => ['icon' => '🐐', 'bg' => '#fff8e1', 'unit' => 'per head'],
    'chickens' => ['icon' => '🐔', 'bg' => '#e8f5e9', 'unit' => 'per kg'],
    'eggs'     => ['icon' => '🥚', 'bg' => '#fffde7', 'unit' => 'per piece'],
];
$defaultMeta = ['icon' => '🐄', 'bg' => '#f3f4f6', 'unit' => 'per head'];

// ── Helper: build tag list from a DB row (stdObject from FETCH_OBJ) ──────────
function buildTags(object $row): array {
    $tags = [];

    if (!empty($row->is_vaccinated)) {
        $tags[] = 'Vaccinated';
    }

    $qty = (int) $row->quantity;
    if ($qty <= 0) {
        $tags[] = 'Out of Stock';
    } elseif ($qty <= 5) {
        $tags[] = 'Low Stock';
    } else {
        $tags[] = 'In Stock';
    }

    $score = (int) $row->health_score;
    if ($score >= 90) {
        $tags[] = 'Excellent Health';
    } elseif ($score >= 70) {
        $tags[] = 'Good Health';
    } elseif ($score > 0) {
        $tags[] = 'Fair Health';
    }

    return $tags;
}

// ── Helper: pick a badge based on a DB row ───────────────────────────────────
function pickBadge(object $row): ?string {
    $score = (int) $row->health_score;
    $qty   = (int) $row->quantity;

    if ($qty <= 0)    return 'Sold Out';
    if ($score >= 95) return 'Top Quality';
    if ($qty <= 5)    return 'Almost Gone';

    return null;
}

// ── Fetch all livestock, newest first ────────────────────────────────────────
try {
    $stmt = $pdo->query(
        "SELECT id, category, name, quantity, price, is_vaccinated,
                condition_notes, health_score, date_created
         FROM livestock
         ORDER BY date_created DESC, id DESC"
    );
    // FETCH_OBJ matches the default set in your Connect class
    $rows = $stmt->fetchAll(PDO::FETCH_OBJ);
} catch (PDOException $e) {
    $rows = [];
    error_log('Shop page DB error: ' . $e->getMessage());
}

// ── Map DB rows → display-ready product array ────────────────────────────────
$products = array_map(function (object $row) use ($catMeta, $defaultMeta): array {
    $cat  = strtolower(trim($row->category));
    $meta = $catMeta[$cat] ?? $defaultMeta;

    return [
        'id'       => (int) $row->id,
        'name'     => htmlspecialchars($row->name, ENT_QUOTES),
        'cat'      => $cat,
        'icon'     => $meta['icon'],
        'bg'       => $meta['bg'],
        'badge'    => pickBadge($row),
        'desc'     => htmlspecialchars($row->condition_notes, ENT_QUOTES),
        'price'    => number_format((int) $row->price),
        'unit'     => $meta['unit'],
        'tags'     => buildTags($row),
        'detail'   => htmlspecialchars($row->condition_notes, ENT_QUOTES)
                      . ' (Health score: ' . (int) $row->health_score . '/100)',
        'quantity' => (int) $row->quantity,
    ];
}, $rows);

// ── Collect unique categories for the filter buttons ────────────────────────
$filterCats = array_unique(array_column($products, 'cat'));
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
            <?php foreach ($filterCats as $cat):
                // Build a human-readable label and pick an icon from catMeta
                $catMeta2 = [
                    'pigs'     => '🐷 Pigs',
                    'goats'    => '🐐 Goats',
                    'chickens' => '🐔 Chickens',
                    'eggs'     => '🥚 Eggs',
                ];
                $label = $catMeta2[$cat] ?? ucfirst($cat);
            ?>
                <button class="filter-btn" onclick="filterProducts('<?= htmlspecialchars($cat) ?>', this)">
                    <?= $label ?>
                </button>
            <?php endforeach; ?>
        </div>

        <!-- Product Grid -->
        <div class="row g-4" id="productGrid">

            <?php if (empty($products)): ?>
                <div class="col-12 text-center py-5">
                    <p class="text-muted">No livestock available at the moment. Check back soon!</p>
                </div>
            <?php endif; ?>

            <?php foreach ($products as $p):
                $outOfStock = $p['quantity'] <= 0;
            ?>
            <div class="col-sm-6 col-lg-4 product-item" data-cat="<?= $p['cat'] ?>">
                <div class="product-card <?= $outOfStock ? 'product-card--sold-out' : '' ?>"
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
                                <span class="tag"><?= htmlspecialchars($tag) ?></span>
                            <?php endforeach; ?>
                        </div>
                        <div class="product-foot">
                            <?php if ($outOfStock): ?>
                                <span class="avail-text" style="color:#e53935;">
                                    <span class="avail-dot" style="background:#e53935;"></span> Out of Stock
                                </span>
                                <button class="btn-order" disabled style="opacity:.5;cursor:not-allowed;">
                                    <i class="bi bi-cart-x"></i> Unavailable
                                </button>
                            <?php else: ?>
                                <span class="avail-text">
                                    <span class="avail-dot"></span>
                                    Available (<?= $p['quantity'] ?> left)
                                </span>
                                <!-- Quantity stepper -->
                                <div class="cart-row" onclick="event.stopPropagation()">
                                    <div class="qty-stepper">
                                        <button type="button" class="qty-btn qty-minus"
                                            onclick="stepQty(this, -1, <?= $p['quantity'] ?>)">&#8722;</button>
                                        <input type="number"
                                               class="qty-input"
                                               id="qty-<?= $p['id'] ?>"
                                               value="1"
                                               min="1"
                                               max="<?= $p['quantity'] ?>"
                                               readonly>
                                        <button type="button" class="qty-btn qty-plus"
                                            onclick="stepQty(this, 1, <?= $p['quantity'] ?>)">&#43;</button>
                                    </div>
                                    <button class="btn-order"
                                        onclick="addToCart(
                                            <?= $p['id'] ?>,
                                            '<?= $user['id'] ?>',
                                            '<?= $p['price'] ?>',
                                            parseInt(document.getElementById('qty-<?= $p['id'] ?>').value),
                                            this
                                        )">
                                        <i class="bi bi-cart-plus"></i> Add to Cart
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>

        </div><!-- /productGrid -->

    </div>
</div>


<script>
    window.isLoggedIn = <?php echo isset($_SESSION['user']) ? 'true' : 'false'; ?>;

</script>