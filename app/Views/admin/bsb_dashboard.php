<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($pageTitle ?? 'BSB Management System Dashboard') ?></title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous"
    >
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >
    <style>
        <?= file_get_contents(APPPATH . 'Views/css/main.css'); ?>
    </style>
</head>
<body class="admin-dash-page">
<div class="admin-dash-layout">
    <aside class="admin-sidebar">
        <div>
            <a href="<?= site_url('/') ?>" class="admin-sidebar-logo">
                <img src="<?= base_url('assets/logo/bsb-logo.png') ?>" alt="BSB">
            </a>

            <div class="admin-sidebar-search">
                <i class="bi bi-search"></i>
                <input type="text" placeholder="Search">
            </div>

            <h6 class="admin-sidebar-title">Main Menu</h6>
            <nav class="admin-menu">
                <a href="#home" class="active"><i class="bi bi-house-fill"></i> Home</a>
                <a href="#manage-product"><i class="bi bi-box-seam"></i> Manage Product</a>
                <a href="#product-list"><i class="bi bi-grid-3x3-gap"></i> Product List</a>
                <a href="#inbox"><i class="bi bi-inbox"></i> Inbox</a>
            </nav>
        </div>

        <div class="admin-profile">
            <div class="admin-profile-avatar"><i class="bi bi-person-circle"></i></div>
            <div class="admin-profile-meta">
                <strong>ID #<?= esc((string) (session('auth_user_id') ?? '0001')) ?></strong>
                <span><?= esc((string) (session('auth_name') ?? 'BSB Admin')) ?></span>
            </div>

            <div class="dropdown ms-auto">
                <button
                    type="button"
                    class="admin-profile-toggle"
                    data-bs-toggle="dropdown"
                    aria-expanded="false"
                    title="Account menu"
                >
                    <i class="bi bi-chevron-down"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item text-danger" href="<?= site_url('/admin/logout') ?>">
                            <i class="bi bi-box-arrow-right me-2"></i>Sign out
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </aside>

    <main class="admin-main">
        <section id="home" class="admin-panel">
            <div class="admin-panel-head">
                <h2><i class="bi bi-house-door-fill"></i> Dashboard Home</h2>
                <p>Overview of your BSB Management System</p>
            </div>

            <div class="admin-stats">
                <article class="stat-card">
                    <h6>Products Count</h6>
                    <p><?= esc((string) ($productsCount ?? 0)) ?></p>
                    <small>Total products in catalog</small>
                </article>
                <article class="stat-card">
                    <h6>Registered Users</h6>
                    <p><?= esc((string) ($registeredUsers ?? 0)) ?></p>
                    <small>Total users in system</small>
                </article>
                <article class="stat-card">
                    <h6>Database Status</h6>
                    <p class="<?= ($dbStatus ?? 'Offline') === 'Online' ? 'status-up' : '' ?>"><?= esc($dbStatus ?? 'Offline') ?></p>
                    <small>Connected and healthy</small>
                </article>
            </div>
        </section>

        <section id="manage-product" class="admin-panel">
            <div class="admin-panel-head">
                <h2><i class="bi bi-plus-square-fill"></i> Manage Product</h2>
                <p>Add a new product to the store catalog</p>
            </div>

            <form class="admin-form-grid" action="<?= site_url('/admin/products/create') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div>
                    <label>Product Name</label>
                    <input type="text" name="product_name" value="<?= esc(old('product_name')) ?>" placeholder="Enter product name">
                </div>

                <div>
                    <label>Price</label>
                    <input type="number" name="price" step="0.01" min="0" value="<?= esc(old('price')) ?>" placeholder="0.00">
                </div>

                <div class="full">
                    <label>Image</label>
                    <input type="file" name="image" accept="image/*">
                </div>

                <div class="full">
                    <label>Description</label>
                    <textarea name="short_description" rows="2" placeholder="Short description"><?= esc(old('short_description')) ?></textarea>
                </div>

                <div class="full">
                    <label>Detailed Description</label>
                    <textarea name="detailed_description" rows="4" placeholder="Detailed product description"><?= esc(old('detailed_description')) ?></textarea>
                </div>

                <div>
                    <label>Product Category</label>
                    <select name="category_id">
                        <option value="" selected disabled>Select category</option>
                        <?php foreach (($categories ?? []) as $cat): ?>
                            <option value="<?= esc((string) $cat['category_id']) ?>" <?= (string) old('category_id') === (string) $cat['category_id'] ? 'selected' : '' ?>>
                                <?= esc($cat['category_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label>Quantity</label>
                    <input type="number" name="stock_qty" min="0" value="<?= esc(old('stock_qty', '0')) ?>">
                </div>

                <div class="admin-form-actions full">
                    <button type="submit" class="btn btn-cta">Add Product</button>
                    <button type="reset" class="btn btn-ghost">Reset</button>
                </div>
            </form>

            <?php if (!empty($product_error)): ?>
                <div class="alert alert-danger py-2 mb-3"><?= esc($product_error) ?></div>
            <?php endif; ?>

            <?php if (!empty($product_success)): ?>
                <div class="alert alert-success py-2 mb-3"><?= esc($product_success) ?></div>
            <?php endif; ?>

            <?php if (isset($validation) && $validation): ?>
                <div class="alert alert-warning py-2 mb-3">
                    Please review the form fields and try again.
                </div>
            <?php endif; ?>
        </section>

        <section id="product-list" class="admin-panel">
            <div class="admin-panel-head">
                <h2><i class="bi bi-grid-fill"></i> Product List</h2>
                <p>Search, filter, sort, edit, and delete products</p>
            </div>

            <form class="admin-list-toolbar" method="get" action="<?= site_url('/admin/dashboard') ?>">
                <div class="toolbar-search">
                    <i class="bi bi-search"></i>
                    <input type="text" name="q" value="<?= esc($filters['q'] ?? '') ?>" placeholder="Search product">
                </div>

                <select name="category_id">
                    <option value="0">All Categories</option>
                    <?php foreach (($categories ?? []) as $cat): ?>
                        <option value="<?= esc((string) $cat['category_id']) ?>" <?= (int)($filters['category_id'] ?? 0) === (int)$cat['category_id'] ? 'selected' : '' ?>>
                            <?= esc($cat['category_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <select name="sort">
                    <option value="latest" <?= ($filters['sort'] ?? 'latest') === 'latest' ? 'selected' : '' ?>>Sort: Latest</option>
                    <option value="name_asc" <?= ($filters['sort'] ?? '') === 'name_asc' ? 'selected' : '' ?>>Sort: Name A-Z</option>
                    <option value="price_asc" <?= ($filters['sort'] ?? '') === 'price_asc' ? 'selected' : '' ?>>Sort: Price Low-High</option>
                    <option value="price_desc" <?= ($filters['sort'] ?? '') === 'price_desc' ? 'selected' : '' ?>>Sort: Price High-Low</option>
                </select>

                <div class="admin-form-actions full">
                    <button type="submit" class="btn btn-cta">Apply</button>
                    <a href="<?= site_url('/admin/dashboard#product-list') ?>" class="btn btn-ghost">Reset</a>
                </div>
            </form>

            <div class="product-card-grid">
                <?php if (! empty($products)): ?>
                    <?php foreach ($products as $product): ?>
                        <article class="product-mini-card">
                            <div class="dropdown card-menu-wrap">
                                <button
                                    class="card-menu-btn"
                                    type="button"
                                    data-bs-toggle="dropdown"
                                    aria-expanded="false"
                                    title="Options"
                                >
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <button
                                            type="button"
                                            class="dropdown-item js-edit-product"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editProductModal"
                                            data-product-id="<?= esc((string) $product['product_id']) ?>"
                                            data-product-name="<?= esc($product['product_name']) ?>"
                                            data-category-id="<?= esc((string) $product['category_id']) ?>"
                                            data-price="<?= esc((string) $product['price']) ?>"
                                            data-stock-qty="<?= esc((string) $product['stock_qty']) ?>"
                                            data-short-description="<?= esc($product['short_description'] ?? '') ?>"
                                            data-detailed-description="<?= esc($product['detailed_description'] ?? '') ?>"
                                        >
                                            <i class="bi bi-pencil-square me-2"></i>Edit
                                        </button>
                                    </li>
                                    <li>
                                        <form action="<?= site_url('/admin/products/delete/' . (int) $product['product_id']) ?>" method="post">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Delete this product?')">
                                                <i class="bi bi-trash me-2"></i>Delete
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                            <div class="thumb">
                                <img src="<?= base_url(!empty($product['main_image']) ? $product['main_image'] : 'assets/placeholder/bsb_product_default.png') ?>" alt="<?= esc($product['product_name']) ?>">
                            </div>
                            <h5><?= esc($product['product_name']) ?></h5>
                            <p>Category: <?= esc($product['category_name'] ?? 'Uncategorized') ?></p>
                            <strong>$<?= esc(number_format((float) $product['price'], 2)) ?></strong>
                        </article>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No products found.</p>
                <?php endif; ?>
            </div>
        </section>

        <section id="inbox" class="admin-panel">
            <div class="admin-panel-head">
                <h2><i class="bi bi-inbox-fill"></i> Inbox</h2>
                <p>Recent notifications and incoming messages</p>
            </div>

            <div class="admin-inbox-list">
                <article>
                    <strong>System Notice</strong>
                    <span>Welcome to BSB Management System dashboard.</span>
                </article>
                <article>
                    <strong>Store Update</strong>
                    <span>Product management module is ready for integration.</span>
                </article>
            </div>
        </section>
    </main>
</div>
</body>
</html>

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"
></script>
<script>
    <?= file_get_contents(APPPATH . 'Views/js/main.js'); ?>
</script>
</body>
</html>

<div class="modal fade" id="editProductModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="editProductForm" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title">Edit Product</h5>
                </div>
                <div class="modal-body">
                    <div class="admin-form-grid">
                        <div>
                            <label>Product Name</label>
                            <input type="text" name="product_name" id="edit_product_name" required>
                        </div>
                        <div>
                            <label>Price</label>
                            <input type="number" name="price" id="edit_price" step="0.01" min="0" required>
                        </div>
                        <div class="full">
                            <label>Image (optional)</label>
                            <input type="file" name="image" accept="image/*">
                        </div>
                        <div class="full">
                            <label>Description</label>
                            <textarea name="short_description" id="edit_short_description" rows="2"></textarea>
                        </div>
                        <div class="full">
                            <label>Detailed Description</label>
                            <textarea name="detailed_description" id="edit_detailed_description" rows="4"></textarea>
                        </div>
                        <div>
                            <label>Product Category</label>
                            <select name="category_id" id="edit_category_id" required>
                                <?php foreach (($categories ?? []) as $cat): ?>
                                    <option value="<?= esc((string) $cat['category_id']) ?>"><?= esc($cat['category_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label>Quantity</label>
                            <input type="number" name="stock_qty" id="edit_stock_qty" min="0" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-cta">Apply</button>
                </div>
            </form>
        </div>
    </div>
</div>