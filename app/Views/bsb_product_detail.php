<?php
$rating = (float) ($product['avg_rating'] ?? 0);
$reviewCount = (int) ($product['rating_count'] ?? 0);
$stockQty = (int) ($product['stock_qty'] ?? 0);
$unitPrice = (float) ($product['price'] ?? 0);
$maxQty = max(1, $stockQty);
?>
<main class="site-shell pb-4">
    <section class="detail-shell">
        <div class="detail-breadcrumb">
            <a href="<?= site_url('/') ?>">HOME</a>
            <span>&gt;</span>
            <a href="<?= site_url('/shop') ?>">SHOP</a>
            <span>&gt;</span>
            <strong><?= esc($product['product_name'] ?? 'PRODUCT') ?></strong>
        </div>

        <div class="detail-layout">
            <section class="detail-gallery">
                <div class="detail-thumbs">
                    <?php foreach (($galleryImages ?? []) as $img): ?>
                        <button type="button" class="detail-thumb" data-image="<?= esc(base_url($img)) ?>">
                            <img src="<?= esc(base_url($img)) ?>" alt="Product thumbnail">
                        </button>
                    <?php endforeach; ?>
                </div>

                <div class="detail-main-image">
                    <img id="detailMainImage" src="<?= esc(base_url($galleryImages[0] ?? 'assets/placeholder/bsb_product_default.png')) ?>" alt="<?= esc($product['product_name'] ?? 'Product') ?>">
                </div>
            </section>

            <section class="detail-summary">
                <p class="detail-category"><?= esc($product['category_name'] ?? 'Uncategorized') ?></p>
                <h1><?= esc($product['product_name'] ?? 'Product') ?></h1>

                <div class="detail-rating-wrap">
                    <div class="detail-stars" aria-label="Rating <?= esc(number_format($rating, 1)) ?> out of 5">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="bi <?= $i <= floor($rating) ? 'bi-star-fill' : 'bi-star' ?>"></i>
                        <?php endfor; ?>
                    </div>
                    <span class="detail-review-count"><?= esc((string) $reviewCount) ?> reviews</span>
                </div>

                <p class="detail-unit-price">$<?= esc(number_format($unitPrice, 2)) ?></p>
                <p class="detail-short"><?= esc($product['short_description'] ?? 'No short description available.') ?></p>
                <p class="detail-stock">Stock: <strong><?= esc((string) $stockQty) ?></strong></p>

                <div class="detail-qty-row">
                    <label for="detailQty">Quantity</label>
                    <div class="detail-qty-control">
                        <button type="button" id="qtyMinus" aria-label="Decrease quantity">-</button>
                        <input
                            id="detailQty"
                            name="qty"
                            form="addToCartForm"
                            type="number"
                            min="1"
                            max="<?= esc((string) $maxQty) ?>"
                            value="1"
                            inputmode="numeric"
                        >
                        <button type="button" id="qtyPlus" aria-label="Increase quantity">+</button>
                    </div>
                </div>

                <p class="detail-total">
                    Total:
                    <strong id="detailTotal" data-unit-price="<?= esc((string) $unitPrice) ?>">
                        $<?= esc(number_format($unitPrice, 2)) ?>
                    </strong>
                </p>

                <div class="detail-actions">
                    <button
                        type="submit"
                        form="addToCartForm"
                        class="btn btn-cta"
                        <?= $stockQty <= 0 ? 'disabled' : '' ?>
                    >
                        <i class="bi bi-cart3 me-2"></i>Add to Cart
                    </button>
                    <button type="button" class="btn btn-ghost detail-wishlist" aria-label="Add to wishlist">
                        <i class="bi bi-heart"></i>
                    </button>
                </div>

                <div class="detail-description-box">
                    <div class="detail-description-head">
                        <h3>Detail</h3>
                    </div>
                    <p><?php
                        $desc = $product['detailed_description'] ?? 'No detailed description available yet.';
                        if (is_array($desc)) {
                            // Flatten multidimensional arrays and join with newlines
                            $desc = implode("\n", array_map(function($item) {
                                return is_array($item) ? implode("\n", $item) : $item;
                            }, $desc));
                        }
                        $desc = (string) $desc;
                        echo nl2br(esc($desc));
                    ?></p>
                </div>
            </section>
        </div>
    </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const mainImage = document.getElementById('detailMainImage');
    const thumbButtons = document.querySelectorAll('.detail-thumb');
    thumbButtons.forEach((btn) => {
        btn.addEventListener('click', function () {
            const src = this.getAttribute('data-image');
            if (mainImage && src) mainImage.src = src;
        });
    });

    const qtyInput = document.getElementById('detailQty');
    const minusBtn = document.getElementById('qtyMinus');
    const plusBtn = document.getElementById('qtyPlus');
    const totalEl = document.getElementById('detailTotal');

    if (!qtyInput || !totalEl) return;

    const max = parseInt(qtyInput.max || '1', 10);
    const unitPrice = parseFloat(totalEl.dataset.unitPrice || '0');

    function clampQty(value) {
        const n = parseInt(value || '1', 10);
        if (Number.isNaN(n)) return 1;
        return Math.min(Math.max(n, 1), max);
    }

    function updateTotal() {
        const qty = clampQty(qtyInput.value);
        qtyInput.value = String(qty);
        const total = unitPrice * qty;
        totalEl.textContent = '$' + total.toFixed(2);
    }

    qtyInput.addEventListener('input', updateTotal);

    if (minusBtn) {
        minusBtn.addEventListener('click', function () {
            qtyInput.value = String(clampQty(String(parseInt(qtyInput.value || '1', 10) - 1)));
            updateTotal();
        });
    }

    if (plusBtn) {
        plusBtn.addEventListener('click', function () {
            qtyInput.value = String(clampQty(String(parseInt(qtyInput.value || '1', 10) + 1)));
            updateTotal();
        });
    }

    updateTotal();
});
</script>

<form id="addToCartForm" method="post" action="<?= site_url('/cart/add') ?>" class="d-none">
    <?= csrf_field() ?>
    <input type="hidden" name="product_id" value="<?= esc((string) ($product['product_id'] ?? 0)) ?>">
</form>