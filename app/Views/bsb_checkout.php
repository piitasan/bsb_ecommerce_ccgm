<main class="site-shell pb-4">
    <section class="checkout-shell">
        <div class="checkout-breadcrumb">
            <a href="<?= site_url('/') ?>">HOME</a>
            <span>&gt;</span>
            <a href="<?= site_url('/cart') ?>">CART</a>
            <span>&gt;</span>
            <strong>CHECK OUT</strong>
        </div>

        <h2 class="checkout-title">ORDER SUMMARY</h2>

        <?php if (session('cart_error')): ?>
            <div class="alert alert-danger py-2"><?= esc(session('cart_error')) ?></div>
        <?php endif; ?>
        <?php if (session('cart_success')): ?>
            <div class="alert alert-success py-2"><?= esc(session('cart_success')) ?></div>
        <?php endif; ?>

        <form method="post" action="<?= site_url('/checkout/place-order') ?>" id="checkoutForm">
            <?= csrf_field() ?>
            <input type="hidden" name="address_lat" id="addressLat">
            <input type="hidden" name="address_lng" id="addressLng">

            <div class="checkout-layout">
                <section class="checkout-items">
                    <?php foreach (($checkoutItems ?? []) as $item): ?>
                        <?php
                            $img = ! empty($item['main_image']) ? $item['main_image'] : 'assets/placeholder/bsb_product_default.png';
                            $lineTotal = (float) $item['unit_price'] * (int) $item['qty'];
                        ?>
                        <article class="checkout-card">
                            <div class="checkout-card-left">
                                <img src="<?= base_url($img) ?>" alt="<?= esc($item['product_name']) ?>">
                            </div>

                            <div class="checkout-card-right">
                                <h4><?= esc($item['product_name']) ?></h4>
                                <p class="checkout-cat"><?= esc($item['category_name'] ?? 'Uncategorized') ?></p>

                                <div class="checkout-price-row">
                                    <strong>$<?= esc(number_format((float) $item['unit_price'], 2)) ?></strong>
                                    <span id="checkoutLineTotal-<?= esc((string) $item['cart_item_id']) ?>">
                                        $<?= esc(number_format($lineTotal, 2)) ?>
                                    </span>
                                </div>

                                <form method="post" action="<?= site_url('/checkout/update-qty') ?>" class="checkout-qty-form">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="cart_item_id" value="<?= esc((string) $item['cart_item_id']) ?>">
                                    <input type="hidden" name="return_to" value="checkout">
                                    <!-- Di ko mapagana <div class="checkout-qty-control">
                                        <button type="button" class="qty-minus">-</button>
                                        <input
                                            type="number"
                                            name="qty"
                                            class="qty-input"
                                            min="1"
                                            max="<?= esc((string) max(1, (int) $item['stock_qty'])) ?>"
                                            value="<?= esc((string) $item['qty']) ?>"
                                            data-unit-price="<?= esc((string) $item['unit_price']) ?>"
                                            data-line-total-id="checkoutLineTotal-<?= esc((string) $item['cart_item_id']) ?>"
                                        >
                                        <button type="button" class="qty-plus">+</button>
                                    </div> -->
                                </form>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </section>

                <aside class="checkout-side">
                    <div class="checkout-summary-box">
                        <?php foreach (($summaryRows ?? []) as $row): ?>
                            <div class="checkout-summary-row">
                                <span><?= esc($row['label']) ?></span>
                                <strong>$<?= esc(number_format((float) $row['amount'], 2)) ?></strong>
                            </div>
                        <?php endforeach; ?>

                        <hr>

                        <div class="checkout-summary-total">
                            <span>Total Amount:</span>
                            <strong>$<?= esc(number_format((float) ($checkoutTotal ?? 0), 2)) ?></strong>
                        </div>
                    </div>

                    <div class="checkout-payment-box">
                        <h5>Mode of Payment</h5>
                        <label class="checkout-radio">
                            <input type="radio" name="payment_method" value="cod" checked>
                            <span>Cash on Delivery</span>
                        </label>
                        <label class="checkout-radio">
                            <input type="radio" name="payment_method" value="gcash">
                            <span>GCash</span>
                        </label>
                    </div>

                    <div class="checkout-address-box">
                        <h5>Delivery Address</h5>
                        <textarea
                            name="delivery_address"
                            id="deliveryAddress"
                            rows="4"
                            required
                            placeholder="Enter your full delivery address"
                        ></textarea>

                        <button type="button" class="btn btn-ghost mt-2" id="useCurrentLocationBtn">
                            Use Current Location
                        </button>
                        <small class="d-block mt-2 text-muted">Location is optional and requires your permission.</small>
                    </div>

                    <button type="submit" class="btn btn-cta w-100">CHECK OUT</button>
                </aside>
            </div>
        </form>
    </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.checkout-qty-form').forEach((form) => {
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
            const target = document.getElementById(input.dataset.lineTotalId);
            if (target) target.textContent = '$' + (unitPrice * parseInt(input.value, 10)).toFixed(2);
        };

        minus?.addEventListener('click', () => {
            input.value = String(clamp(String(parseInt(input.value || '1', 10) - 1)));
            updateLinePreview();
            form.submit();
        });

        plus?.addEventListener('click', () => {
            input.value = String(clamp(String(parseInt(input.value || '1', 10) + 1)));
            updateLinePreview();
            form.submit();
        });

        input.addEventListener('change', () => {
            updateLinePreview();
            form.submit();
        });

        updateLinePreview();
    });

    const locBtn = document.getElementById('useCurrentLocationBtn');
    const latInput = document.getElementById('addressLat');
    const lngInput = document.getElementById('addressLng');
    const addressBox = document.getElementById('deliveryAddress');

    locBtn?.addEventListener('click', function () {
        if (!navigator.geolocation) {
            alert('Geolocation is not supported by this browser.');
            return;
        }

        navigator.geolocation.getCurrentPosition(
            function (position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                latInput.value = String(lat);
                lngInput.value = String(lng);

                if (addressBox && !addressBox.value.trim()) {
                    addressBox.value = 'Lat: ' + lat.toFixed(6) + ', Lng: ' + lng.toFixed(6);
                }
            },
            function () {
                alert('Location permission denied or unavailable.');
            },
            { enableHighAccuracy: true, timeout: 10000 }
        );
    });
});
</script>