<?php // pages/products.php
$products = [
    ['P001', 'Ampalaya Bundle', 'Vegetables', '₱240', 42, 'In Stock'],
    ['P002', 'Sitaw Pack (1kg)', 'Vegetables', '₱180', 15, 'Low Stock'],
    ['P003', 'Kangkong Bunch', 'Vegetables', '₱90', 0, 'Out of Stock'],
    ['P004', 'Kamote (5kg)', 'Root Crops', '₱350', 88, 'In Stock'],
    ['P005', 'Mixed Greens Box', 'Vegetables', '₱520', 5, 'Low Stock'],
    ['P006', 'Banana (Lakatan)', 'Fruits', '₱210', 60, 'In Stock'],
    ['P007', 'Pineapple (Medium)', 'Fruits', '₱150', 34, 'In Stock'],
    ['P008', 'Sweet Potato Leaves', 'Vegetables', '₱75', 0, 'Out of Stock'],
];
?>

<!-- Page Header -->
<div class="page-header mb-4">
    <div>
        <h5 class="page-heading mb-1">Products</h5>
        <p class="text-muted mb-0 small">Manage your product inventory</p>
    </div>
    <button class="btn btn-green" data-bs-toggle="modal" data-bs-target="#addProductModal">
        <i class="bi bi-plus-lg me-1"></i> Add Product
    </button>
</div>

<!-- Filter Bar -->
<div class="card-panel mb-4">
    <div class="row g-2 align-items-center">
        <div class="col-12 col-md-5">
            <div class="search-bar">
                <i class="bi bi-search"></i>
                <input type="text" id="productSearch" class="form-control" placeholder="Search products…" />
            </div>
        </div>
        <div class="col-6 col-md-3">
            <select class="form-select" id="categoryFilter">
                <option value="">All Categories</option>
                <option>Vegetables</option>
                <option>Fruits</option>
                <option>Root Crops</option>
            </select>
        </div>
        <div class="col-6 col-md-3">
            <select class="form-select" id="stockFilter">
                <option value="">All Status</option>
                <option>In Stock</option>
                <option>Low Stock</option>
                <option>Out of Stock</option>
            </select>
        </div>
    </div>
</div>

<!-- Products Table -->
<div class="card-panel">
    <div class="table-responsive">
        <table class="table admin-table" id="productsTable">
            <thead>
                <tr>
                    <th>SKU</th>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $p): ?>
                <tr data-name="<?= strtolower($p[1]) ?>" data-category="<?= $p[2] ?>" data-status="<?= $p[5] ?>">
                    <td class="fw-semibold text-green small"><?= $p[0] ?></td>
                    <td><?= $p[1] ?></td>
                    <td><span class="category-tag"><?= $p[2] ?></span></td>
                    <td class="fw-semibold"><?= $p[3] ?></td>
                    <td><?= $p[4] ?></td>
                    <td><span class="status-badge status-<?= strtolower(str_replace(' ', '-', $p[5])) ?>"><?= $p[5] ?></span></td>
                    <td>
                        <div class="action-btns">
                            <button class="action-btn edit" title="Edit"><i class="bi bi-pencil"></i></button>
                            <button class="action-btn delete" title="Delete"><i class="bi bi-trash"></i></button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
        <small class="text-muted">Showing <?= count($products) ?> products</small>
        <nav>
            <ul class="pagination pagination-sm mb-0">
                <li class="page-item disabled"><a class="page-link" href="#">&laquo;</a></li>
                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item"><a class="page-link" href="#">&raquo;</a></li>
            </ul>
        </nav>
    </div>
</div>

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content admin-modal">
            <div class="modal-header">
                <h6 class="modal-title fw-bold" id="addProductLabel"><i class="bi bi-plus-circle me-2 text-green"></i>Add New Product</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Product Name</label>
                    <input type="text" class="form-control" placeholder="e.g. Ampalaya Bundle" />
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <label class="form-label">Category</label>
                        <select class="form-select">
                            <option>Vegetables</option>
                            <option>Fruits</option>
                            <option>Root Crops</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label">Price (₱)</label>
                        <input type="number" class="form-control" placeholder="0.00" />
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Stock Quantity</label>
                    <input type="number" class="form-control" placeholder="0" />
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" rows="3" placeholder="Short product description…"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-green btn-sm">Save Product</button>
            </div>
        </div>
    </div>
</div>

<script>
// Live search + filter for products table
(function () {
    const searchInput  = document.getElementById('productSearch');
    const catFilter    = document.getElementById('categoryFilter');
    const stockFilter  = document.getElementById('stockFilter');
    const rows         = document.querySelectorAll('#productsTable tbody tr');

    function applyFilters() {
        const q   = searchInput.value.toLowerCase();
        const cat = catFilter.value;
        const st  = stockFilter.value;
        rows.forEach(function (row) {
            const name    = row.dataset.name    || '';
            const category= row.dataset.category|| '';
            const status  = row.dataset.status  || '';
            const matchQ   = !q   || name.includes(q);
            const matchCat = !cat || category === cat;
            const matchSt  = !st  || status === st;
            row.style.display = (matchQ && matchCat && matchSt) ? '' : 'none';
        });
    }

    searchInput.addEventListener('input',  applyFilters);
    catFilter.addEventListener('change',   applyFilters);
    stockFilter.addEventListener('change', applyFilters);
})();
</script>
