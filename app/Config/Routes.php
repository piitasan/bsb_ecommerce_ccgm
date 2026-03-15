<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->match(['GET', 'POST'], '/admin/login', 'Admin::login');
$routes->get('/admin/dashboard', 'Admin::dashboard');
$routes->post('/admin/products/create', 'Admin::createProduct');
$routes->get('/admin/logout', 'Admin::logout');
$routes->post('/admin/products/update/(:num)', 'Admin::updateProduct/$1');
$routes->post('/admin/products/delete/(:num)', 'Admin::deleteProduct/$1');

$routes->match(['GET', 'POST'], '/signin', 'Auth::signin');
$routes->match(['GET', 'POST'], '/signup', 'Auth::signup');
$routes->get('/profile', 'Auth::profile');
$routes->get('/logout', 'Auth::logout');

$routes->get('/shop', 'Shop::index');
$routes->get('/shop/product/(:segment)', 'Shop::detail/$1');
$routes->get('/cart', 'Cart::index');
$routes->post('/cart/add', 'Cart::add');
$routes->post('/cart/update-qty', 'Cart::updateQty');
$routes->post('/cart/remove', 'Cart::remove');
$routes->post('/cart/proceed', 'Cart::proceed');
$routes->get('/checkout', 'Cart::checkout');
$routes->post('/checkout/place-order', 'Cart::placeOrder');
$routes->post('/checkout/update-qty', 'Cart::updateQty');