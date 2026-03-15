<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($pageTitle ?? 'Byte-Sized Bakes') ?></title>

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
<body>
<header class="site-shell py-3">
    <nav class="navbar navbar-expand-xl top-nav" aria-label="Main navigation">
        <a class="navbar-brand brand m-0" href="<?= site_url('/') ?>">
            <img src="<?= base_url('assets/logo/bsb-logo.png') ?>" alt="BSB Logo">
        </a>

        <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse gap-3" id="mainNavbar">
            <form class="search-wrap d-flex flex-grow-1 me-xl-3" action="<?= site_url('/') ?>" method="get" role="search">
                <input type="search" name="q" placeholder="Search 3D pastry assets..." aria-label="Search products">
                <button type="submit" aria-label="Search">
                    <i class="bi bi-search"></i>
                </button>
            </form>

            <ul class="navbar-nav nav-links align-items-xl-center">
                <li class="nav-item"><a class="nav-link p-0" href="<?= site_url('/') ?>">Home</a></li>
                <li class="nav-item"><a class="nav-link p-0" href="<?= site_url('/#about') ?>">About</a></li>
                <li class="nav-item"><a class="nav-link p-0" href="<?= site_url('/shop') ?>">Shop</a></li>
            </ul>

            <span class="nav-divider d-none d-xl-inline-block" aria-hidden="true"></span>

            <div class="nav-actions ms-xl-auto">
                <a class="icon-btn" href="#" aria-label="Wishlist"><i class="bi bi-heart-fill"></i></a>
                <a class="icon-btn" href="<?= site_url('/cart') ?>" aria-label="Cart"><i class="bi bi-cart3"></i></a>

                <?php if (session('isUserLoggedIn')): ?>
                    <a class="icon-btn" href="<?= site_url('/profile') ?>" aria-label="Profile">
                        <i class="bi bi-person-circle"></i>
                    </a>
                <?php else: ?>
                    <a class="btn btn-soft" href="<?= site_url('/signin') ?>">Sign in</a>
                    <a class="btn btn-soft btn-accent" href="<?= site_url('/signup') ?>">Signup</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
</header>