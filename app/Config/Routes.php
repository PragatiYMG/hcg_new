<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Admin routes
$routes->get('/admin/login', 'Admin::login');
$routes->post('/admin/authenticate', 'Admin::authenticate');
$routes->get('/admin/dashboard', 'Admin::dashboard');
$routes->get('/admin/logout', 'Admin::logout');
$routes->get('/admin/settings', 'Admin::settings');
$routes->post('/admin/settings/update', 'Admin::updateSettings');
