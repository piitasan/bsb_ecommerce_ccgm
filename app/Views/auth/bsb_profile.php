<main class="site-shell profile-shell pb-4">
    <div class="profile-breadcrumb">
        <a href="<?= site_url('/') ?>">HOME</a>
        <span>&gt;</span>
        <a href="<?= site_url('/profile') ?>">PROFILE</a>
        <span>&gt;</span>
        <strong><?= esc(strtoupper($activeTab === 'overview' ? 'PROFILE' : $activeTab)) ?></strong>
    </div>

    <section class="profile-layout">
        <aside class="profile-sidebar">
            <h3><i class="bi bi-person-circle"></i> <?= esc($user['uname'] ?? 'User') ?></h3>
            <p>Email: <?= esc($user['email'] ?? '-') ?></p>
            <hr>
            <a class="<?= $activeTab === 'history' ? 'active' : '' ?>" href="<?= site_url('/profile?tab=history') ?>"><i class="bi bi-clock-history"></i> Purchase History</a>
            <a class="<?= $activeTab === 'wishlist' ? 'active' : '' ?>" href="<?= site_url('/profile?tab=wishlist') ?>"><i class="bi bi-heart-fill"></i> Wishlist</a>
            <a class="<?= $activeTab === 'payment' ? 'active' : '' ?>" href="<?= site_url('/profile?tab=payment') ?>"><i class="bi bi-credit-card-2-front"></i> Payment Methods</a>
            <a class="<?= $activeTab === 'security' ? 'active' : '' ?>" href="<?= site_url('/profile?tab=security') ?>"><i class="bi bi-shield-lock"></i> Security</a>
            <hr>
            <a href="<?= site_url('/logout') ?>"><i class="bi bi-box-arrow-right"></i> Log out</a>
        </aside>

        <section class="profile-content">
            <?php if ($activeTab === 'overview'): ?>
                <h2><i class="bi bi-person-circle"></i> <?= esc($user['uname'] ?? 'User') ?></h2>
                <hr>
                <h4>PERSONAL INFORMATION</h4>
                <div class="profile-grid">
                    <div><strong>FIRST NAME</strong><p><?= esc($user['fname'] ?? '-') ?></p></div>
                    <div><strong>LAST NAME</strong><p><?= esc($user['lname'] ?? '-') ?></p></div>
                    <div><strong>EMAIL ADDRESS</strong><p><?= esc($user['email'] ?? '-') ?></p></div>
                </div>
            <?php elseif ($activeTab === 'history'): ?>
                <h2>Purchase History</h2>
                <hr>

                <?php if (!empty($historyByDay)): ?>
                    <?php foreach ($historyByDay as $day => $dayOrders): ?>
                        <h5 class="profile-history-date"><?= esc($day) ?></h5>

                        <?php foreach ($dayOrders as $order): ?>
                            <article class="profile-order-group">
                                <p class="profile-order-id"><strong>Order ID:</strong> <?= esc($order['order_number']) ?></p>

                                <?php foreach ($order['items'] as $item): ?>
                                    <?php $img = !empty($item['main_image']) ? $item['main_image'] : 'assets/placeholder/bsb_product_default.png'; ?>
                                    <div class="profile-order-item">
                                        <div class="profile-order-thumb">
                                            <img src="<?= base_url($img) ?>" alt="<?= esc($item['product_name']) ?>">
                                        </div>

                                        <div class="profile-order-meta">
                                            <h6><?= esc($item['product_name']) ?></h6>
                                            <p>Category: <?= esc($item['category_name'] ?? 'Uncategorized') ?></p>
                                            <p>Quantity: <?= esc((string) $item['qty']) ?></p>
                                            <p>Payment: <?= esc(strtoupper((string) $order['payment_method'])) ?></p>
                                            <p>Total: $<?= esc(number_format((float) $item['line_total'], 2)) ?></p>
                                            <p>Status: <?= esc($order['order_status']) ?></p>
                                            <button type="button" class="btn btn-ghost btn-sm">Leave a Review</button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </article>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No purchases yet.</p>
                <?php endif; ?>
            <?php else: ?>
                <h2><?= esc(ucfirst($activeTab)) ?></h2>
                <hr>
                <p>This section will be implemented next.</p>
            <?php endif; ?>
        </section>
    </section>
</main>