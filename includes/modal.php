<!-- Product Detail Modal -->
<div class="modal-overlay" id="productModal" onclick="closeModalOutside(event)">
    <div class="modal-box">
        <button class="modal-close" onclick="closeModal()">
            <i class="bi bi-x"></i>
        </button>
        <div style="font-size:3.5rem;margin-bottom:0.5rem" id="modalIcon">🐷</div>
        <h4 id="modalName">Product Name</h4>
        <div class="price-big" id="modalPrice">₱0</div>
        <p id="modalDesc">Description here.</p>
        <div class="meta" id="modalTags"></div>
        <div class="modal-hint">
            <p><i class="bi bi-info-circle"></i> &nbsp;To place an order, complete the order form with your preferred livestock and delivery details.</p>
        </div>
        <div style="display:flex;gap:10px;margin-top:1.25rem">
            <button class="btn-add-cart-modal" id="modalAddCartBtn" onclick="addToCartFromModal()">
                <i class="bi bi-cart-plus"></i> Add to Cart
            </button>
            <a href="index.php?page=contact" class="btn-order-modal" style="flex:1">
                <i class="bi bi-bag-check"></i> Order Now
            </a>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div id="toast">
    <i class="bi bi-check-circle-fill" style="color:#81c784"></i>
    <span id="toastMsg">Done!</span>
</div>
