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

// Temporary redirect for missing Users module
$routes->get('/admin/users', 'Admin::users');

// Areas routes
$routes->get('/admin/areas', 'Admin::areas');
$routes->get('/admin/areas/create', 'Admin::createArea');
$routes->post('/admin/areas/store', 'Admin::storeArea');
$routes->get('/admin/areas/edit/(:num)', 'Admin::editArea/$1');
$routes->post('/admin/areas/update/(:num)', 'Admin::updateArea/$1');
$routes->get('/admin/areas/delete/(:num)', 'Admin::deleteArea/$1');

// Taxes routes (separate controller)
$routes->get('/admin/taxes', 'Taxes::index');
$routes->get('/admin/taxes/create', 'Taxes::create');
$routes->post('/admin/taxes/store', 'Taxes::store');
$routes->get('/admin/taxes/edit/(:num)', 'Taxes::edit/$1');
$routes->post('/admin/taxes/update/(:num)', 'Taxes::update/$1');
$routes->get('/admin/taxes/delete/(:num)', 'Taxes::delete/$1');

// Tax Types routes (separate controller)
$routes->get('/admin/tax-types', 'TaxTypes::index');
$routes->get('/admin/tax-types/create', 'TaxTypes::create');
$routes->post('/admin/tax-types/store', 'TaxTypes::store');
$routes->get('/admin/tax-types/edit/(:num)', 'TaxTypes::edit/$1');
$routes->post('/admin/tax-types/update/(:num)', 'TaxTypes::update/$1');
$routes->get('/admin/tax-types/delete/(:num)', 'TaxTypes::delete/$1');
