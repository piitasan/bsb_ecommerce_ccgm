<main class="site-shell pb-4">
    <section class="cart-shell">
        <div class="cart-breadcrumb">
            <a href="<?= site_url('/') ?>">HOME</a>
            <span>&gt;</span>
            <strong>CART</strong>
        </div>

        <h2 class="cart-title"><i class="bi bi-cart3 me-2"></i>SHOPPING CART: <?= esc((string) ($cartQtyCount ?? 0)) ?></h2>

        <?php if (session('cart_error')): ?>
            <div class="alert alert-danger py-2"><?= esc(session('cart_error')) ?></div>
        <?php endif; ?>
        <?php if (session('cart_success')): ?>
            <div class="alert alert-success py-2"><?= esc(session('cart_success')) ?></div>
        <?php endif; ?>

        <?php if (! empty($cartItems)): ?>
            <div class="cart-grid">
                <?php foreach ($cartItems as $item): ?>
                    <?php
                        $lineTotal = (float) $item['unit_price'] * (int) $item['qty'];
                        $img = ! empty($item['main_image']) ? $item['main_image'] : 'assets/placeholder/bsb_product_default.png';
                    ?>
                    <article class="cart-card">
                        <div class="cart-card-left">
                            <img src="<?= base_url($img) ?>" alt="<?= esc($item['product_name']) ?>">
                        </div>

                        <div class="cart-card-right">
                            <div class="cart-card-top">
                                <label class="cart-check">
                                    <input
                                        type="checkbox"
                                        name="cart_item_ids[]"
                                        value="<?= esc((string) $item['cart_item_id']) ?>"
                                        form="proceedForm"
                                        class="cart-item-check"
                                        data-line-total="<?= esc((string) $lineTotal) ?>"
                                    >
                                </label>

                                <form method="post" action="<?= site_url('/cart/remove') ?>" class="d-inline">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="cart_item_id" value="<?= esc((string) $item['cart_item_id']) ?>">
                                    <button type="submit" class="cart-remove-btn" title="Remove item">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>

                            <h4><?= esc($item['product_name']) ?></h4>
                            <p class="cart-cat"><?= esc($item['category_name'] ?? 'Uncategorized') ?></p>

                            <div class="cart-price-row">
                                <strong>$<?= esc(number_format((float) $item['unit_price'], 2)) ?></strong>
                                <span id="lineTotal-<?= esc((string) $item['cart_item_id']) ?>">
                                    $<?= esc(number_format($lineTotal, 2)) ?>
                                </span>
                            </div>

                            <form method="post" action="<?= site_url('/cart/update-qty') ?>" class="cart-qty-form">
                                <?= csrf_field() ?>
                                <input type="hidden" name="cart_item_id" value="<?= esc((string) $item['cart_item_id']) ?>">
                                <div class="cart-qty-control">
                                    <button type="button" class="qty-minus">-</button>
                                    <input
                                        type="number"
                                        name="qty"
                                        class="qty-input"
                                        min="1"
                                        max="<?= esc((string) max(1, (int) $item['stock_qty'])) ?>"
                                        value="<?= esc((string) $item['qty']) ?>"
                                        data-unit-price="<?= esc((string) $item['unit_price']) ?>"
                                        data-line-total-id="lineTotal-<?= esc((string) $item['cart_item_id']) ?>"
                                    >
                                    <button type="button" class="qty-plus">+</button>
                                </div>
                            </form>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <div class="cart-bottom">
                <p class="cart-subtotal">
                    Selected Subtotal: <strong id="selectedSubtotal">$0.00</strong>
                </p>

                <form id="proceedForm" method="post" action="<?= site_url('/cart/proceed') ?>">
                    <?= csrf_field() ?>
                    <button id="proceedBtn" type="submit" class="btn btn-cta" disabled>
                        Proceed to Checkout
                    </button>
                </form>
            </div>
        <?php else: ?>
            <div class="cart-empty">
                <p>Your cart is empty.</p>
                <a href="<?= site_url('/shop') ?>" class="btn btn-ghost">Go to Shop</a>
            </div>
        <?php endif; ?>
    </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.cart-qty-form').forEach((form) => {
        const input = form.querySelector('.qty-input');
        const minus = form.querySelector('.qty-minus');
        const plus = form.querySelector('.qty-plus');

        if (!input) return;

        const max = parseInt(input.max || '1', 10);

        const clamp = (v) => {
            const n = parseInt(v || '1', 10);
            if (Number.isNaN(n)) return 1;
            return Math.min(Math.max(n, 1), max);
        };

        const updateLinePreview = () => {
            input.value = String(clamp(input.value));
            const unitPrice = parseFloat(input.dataset.unitPrice || '0');
            const lineTotalId = input.dataset.lineTotalId;
            const lineTotalEl = document.getElementById(lineTotalId);
            if (lineTotalEl) {
                lineTotalEl.textContent = '$' + (unitPrice * parseInt(input.value, 10)).toFixed(2);
            }
        };

        if (minus) {
            minus.addEventListener('click', () => {
                input.value = String(clamp(String(parseInt(input.value || '1', 10) - 1)));
                updateLinePreview();
                form.submit();
            });
        }

        if (plus) {
            plus.addEventListener('click', () => {
                input.value = String(clamp(String(parseInt(input.value || '1', 10) + 1)));
                updateLinePreview();
                form.submit();
            });
        }

        input.addEventListener('change', () => {
            updateLinePreview();
            form.submit();
        });

        updateLinePreview();
    });
});

const checks = document.querySelectorAll('.cart-item-check');
const proceedBtn = document.getElementById('proceedBtn');
const selectedSubtotalEl = document.getElementById('selectedSubtotal');

function updateSelectedSubtotal() {
    let total = 0;
    let checkedCount = 0;

    checks.forEach((chk) => {
        if (chk.checked) {
            total += parseFloat(chk.dataset.lineTotal || '0');
            checkedCount++;
        }
    });

    if (selectedSubtotalEl) {
        selectedSubtotalEl.textContent = '$' + total.toFixed(2);
    }

    if (proceedBtn) {
        proceedBtn.disabled = checkedCount === 0;
    }
}

checks.forEach((chk) => {
    chk.addEventListener('change', updateSelectedSubtotal);
});

updateSelectedSubtotal();
</script>