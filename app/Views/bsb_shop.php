<main class="site-shell pb-4">
    <section class="shop-shell">
        <div class="shop-breadcrumb">
            <a href="<?= site_url('/') ?>">HOME</a>
            <span>&gt;</span>
            <strong>SHOP</strong>
        </div>

        <div class="shop-layout">
            <aside class="shop-filter">
                <div class="shop-filter-head">
                    <h3>Filter</h3>
                    <i class="bi bi-funnel"></i>
                </div>

                <form method="get" action="<?= site_url('/shop') ?>" class="shop-filter-form">
                    <label class="shop-filter-label" for="shop_q">Search</label>
                    <input
                        id="shop_q"
                        type="text"
                        name="q"
                        value="<?= esc($filters['q'] ?? '') ?>"
                        placeholder="Search products..."
                        class="shop-input"
                    >

                    <label class="shop-filter-label">Category</label>
                    <div class="shop-checklist">
                        <?php foreach (($categories ?? []) as $cat): ?>
                            <?php $catId = (int) $cat['category_id']; ?>
                            <label class="shop-check">
                                <input
                                    type="checkbox"
                                    name="category[]"
                                    value="<?= esc((string) $catId) ?>"
                                    <?= in_array($catId, $filters['category'] ?? [], true) ? 'checked' : '' ?>
                                >
                                <span><?= esc($cat['category_name']) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>

                    <label class="shop-filter-label" for="shop_sort">Sort</label>
                    <select id="shop_sort" name="sort" class="shop-input">
                        <option value="latest" <?= ($filters['sort'] ?? '') === 'latest' ? 'selected' : '' ?>>Latest</option>
                        <option value="name_asc" <?= ($filters['sort'] ?? '') === 'name_asc' ? 'selected' : '' ?>>Name A-Z</option>
                        <option value="price_asc" <?= ($filters['sort'] ?? '') === 'price_asc' ? 'selected' : '' ?>>Price Low-High</option>
                        <option value="price_desc" <?= ($filters['sort'] ?? '') === 'price_desc' ? 'selected' : '' ?>>Price High-Low</option>
                        <option value="rating_desc" <?= ($filters['sort'] ?? '') === 'rating_desc' ? 'selected' : '' ?>>Top Rated</option>
                    </select>

                    <div class="shop-filter-actions">
                        <button type="submit" class="btn btn-cta">Apply</button>
                        <a href="<?= site_url('/shop') ?>" class="btn btn-ghost">Reset</a>
                    </div>
                </form>
            </aside>

            <section class="shop-results">
                <div class="shop-results-head">
                    <h2><?= esc((string) ($resultCount ?? 0)) ?> RESULTS</h2>
                </div>

                <?php if (! empty($products)): ?>
                    <div class="shop-grid">
                        <?php foreach ($products as $product): ?>
                            <?php
                                $image = ! empty($product['main_image']) ? $product['main_image'] : 'assets/placeholder/bsb_product_default.png';
                                $rating = (float) ($product['avg_rating'] ?? 0);
                                $soldCount = isset($product['sold_count']) ? (int) $product['sold_count'] : (int) ($product['rating_count'] ?? 0);
                            ?>
                            <article class="shop-card">
                                <div class="shop-card-thumb">
                                    <img src="<?= base_url($image) ?>" alt="<?= esc($product['product_name']) ?>">
                                </div>

                                <div class="shop-card-meta">
                                    <p class="shop-cat"><?= esc($product['category_name'] ?? 'Uncategorized') ?></p>
                                    <h4><?= esc($product['product_name']) ?></h4>
                                    <p class="shop-desc"><?= esc($product['short_description'] ?? 'No description yet.') ?></p>

                                    <div class="shop-rating-row">
                                        <span class="shop-rating"><i class="bi bi-star-fill"></i> <?= esc(number_format($rating, 1)) ?></span>
                                    </div>

                                    <div class="shop-price-row">
                                        <strong>$<?= esc(number_format((float) $product['price'], 2)) ?></strong>
                                        <span><?= esc((string) $soldCount) ?> Sold</span>
                                    </div>

                                    <a
                                        href="<?= site_url('/shop/product/' . rawurlencode(! empty($product['product_slug']) ? $product['product_slug'] : (string) $product['product_id'])) ?>"
                                        class="btn btn-ghost shop-cart-btn"
                                    >
                                        <i class="bi bi-eye me-2"></i>View Product
                                    </a>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="shop-empty">
                        <p>No products matched your filters.</p>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </section>
</main>