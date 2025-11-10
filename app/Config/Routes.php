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

// Areas routes
$routes->get('/admin/areas', 'Admin::areas');
$routes->get('/admin/areas/create', 'Admin::createArea');
$routes->post('/admin/areas/store', 'Admin::storeArea');
$routes->get('/admin/areas/edit/(:num)', 'Admin::editArea/$1');
$routes->post('/admin/areas/update/(:num)', 'Admin::updateArea/$1');
$routes->get('/admin/areas/delete/(:num)', 'Admin::deleteArea/$1');
