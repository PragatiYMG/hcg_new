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

// Users routes (admin)
$routes->get('/admin/users', 'Users::index');
$routes->get('/admin/users/create', 'Users::create');
$routes->post('/admin/users/store', 'Users::store');
$routes->get('/admin/users/edit/(:num)', 'Users::edit/$1');
$routes->post('/admin/users/update/(:num)', 'Users::update/$1');
$routes->get('/admin/users/delete/(:num)', 'Users::delete/$1');

// Areas routes
$routes->get('/admin/areas', 'Admin::areas');
$routes->get('/admin/areas/create', 'Admin::createArea');
$routes->post('/admin/areas/store', 'Admin::storeArea');
$routes->get('/admin/areas/edit/(:num)', 'Admin::editArea/$1');
$routes->post('/admin/areas/update/(:num)', 'Admin::updateArea/$1');
$routes->get('/admin/areas/delete/(:num)', 'Admin::deleteArea/$1');

// Taxes routes (AJAX-based management)
$routes->get('/admin/taxes', 'Taxes::index');
$routes->get('/admin/taxes/getTaxes', 'Taxes::getTaxes');
$routes->post('/admin/taxes/store', 'Taxes::store');
$routes->post('/admin/taxes/update/(:num)', 'Taxes::update/$1');

// Societies routes (separate controller)
$routes->get('/admin/societies', 'Societies::index');
$routes->get('/admin/societies/create', 'Societies::create');
$routes->post('/admin/societies/store', 'Societies::store');
$routes->get('/admin/societies/edit/(:num)', 'Societies::edit/$1');
$routes->post('/admin/societies/update/(:num)', 'Societies::update/$1');
$routes->get('/admin/societies/delete/(:num)', 'Societies::delete/$1');

// Rates routes (AJAX-based management)
$routes->get('/admin/rates', 'Rates::index');
$routes->get('/admin/rates/getRates', 'Rates::getRates');
$routes->get('/admin/rates/getTaxRates', 'Rates::getTaxRates');
$routes->post('/admin/rates/store', 'Rates::store');
$routes->post('/admin/rates/update/(:num)', 'Rates::update/$1');

// Charges routes (AJAX-based management)
$routes->get('/admin/charges', 'Charges::index');
$routes->get('/admin/charges/getCharges', 'Charges::getCharges');
$routes->post('/admin/charges/store', 'Charges::store');
$routes->post('/admin/charges/update/(:num)', 'Charges::update/$1');

// Bills routes (edit-focused)
$routes->get('/admin/bills/edit/(:num)', 'Bills::edit/$1');
$routes->post('/admin/bills/update/(:num)', 'Bills::update/$1');

// Countries routes (separate controller)
$routes->get('/admin/countries', 'Countries::index');
$routes->get('/admin/countries/create', 'Countries::create');
$routes->post('/admin/countries/store', 'Countries::store');
$routes->get('/admin/countries/edit/(:num)', 'Countries::edit/$1');
$routes->post('/admin/countries/update/(:num)', 'Countries::update/$1');
$routes->get('/admin/countries/delete/(:num)', 'Countries::delete/$1');

// States routes (separate controller)
$routes->get('/admin/states', 'States::index');
$routes->get('/admin/states/create', 'States::create');
$routes->post('/admin/states/store', 'States::store');
$routes->get('/admin/states/edit/(:num)', 'States::edit/$1');
$routes->post('/admin/states/update/(:num)', 'States::update/$1');
$routes->get('/admin/states/delete/(:num)', 'States::delete/$1');
$routes->get('/admin/states/getByCountry/(:num)', 'States::getByCountry/$1');

// Cities routes (separate controller)
$routes->get('/admin/cities', 'Cities::index');
$routes->get('/admin/cities/create', 'Cities::create');
$routes->post('/admin/cities/store', 'Cities::store');
$routes->get('/admin/cities/edit/(:num)', 'Cities::edit/$1');
$routes->post('/admin/cities/update/(:num)', 'Cities::update/$1');
$routes->get('/admin/cities/delete/(:num)', 'Cities::delete/$1');
$routes->get('/admin/cities/getByState/(:num)', 'Cities::getByState/$1');

// Banks routes (DataTable with AJAX CRUD)
$routes->get('/admin/banks', 'Banks::index');
$routes->get('/admin/banks/getTableData', 'Banks::getTableData');
$routes->get('/admin/banks/getBank/(:num)', 'Banks::getBank/$1');
$routes->post('/admin/banks/store', 'Banks::store');
$routes->post('/admin/banks/update/(:num)', 'Banks::update/$1');
$routes->get('/admin/banks/getBanksForDropdown', 'Banks::getBanksForDropdown');

// Images routes (DataTable with AJAX CRUD)
$routes->get('/admin/images', 'Images::index');
$routes->get('/admin/images/getTableData', 'Images::getTableData');
$routes->get('/admin/images/getImage/(:num)', 'Images::getImage/$1');
$routes->post('/admin/images/store', 'Images::store');
$routes->post('/admin/images/update/(:num)', 'Images::update/$1');
$routes->get('/admin/images/getImagesForDropdown', 'Images::getImagesForDropdown');

// Bills routes (Enhanced with versioning)
$routes->get('/admin/bills', 'Bills::index');
$routes->get('/admin/bills/getTableData', 'Bills::getTableData');
$routes->get('/admin/bills/create', 'Bills::create');
$routes->post('/admin/bills/store', 'Bills::store');
$routes->get('/admin/bills/view/(:num)', 'Bills::view/$1');
$routes->get('/admin/bills/edit/(:num)', 'Bills::edit/$1');
$routes->post('/admin/bills/update/(:num)', 'Bills::update/$1');
$routes->post('/admin/bills/activate/(:num)', 'Bills::activate/$1');
$routes->post('/admin/bills/duplicate/(:num)', 'Bills::duplicate/$1');
$routes->get('/admin/bills/getActiveBill', 'Bills::getActiveBill');
$routes->get('/admin/bills/getBillByDate', 'Bills::getBillByDate');

// Connection Fees routes (DataTable with AJAX CRUD)
$routes->get('/admin/connection-fees', 'ConnectionFees::index');
$routes->get('/admin/connection-fees/get-table-data', 'ConnectionFees::getTableData');
$routes->get('/admin/connection-fees/create', 'ConnectionFees::create');
$routes->post('/admin/connection-fees/store', 'ConnectionFees::store');
$routes->get('/admin/connection-fees/edit/(:num)', 'ConnectionFees::edit/$1');
$routes->post('/admin/connection-fees/update/(:num)', 'ConnectionFees::update/$1');
$routes->get('/admin/connection-fees/get-active-fee', 'ConnectionFees::getActiveFee');

// Connection Statuses routes (DataTable with AJAX CRUD - no delete)
$routes->get('/admin/connection-statuses', 'ConnectionStatuses::index');
$routes->get('/admin/connection-statuses/get-table-data', 'ConnectionStatuses::getTableData');
$routes->get('/admin/connection-statuses/create', 'ConnectionStatuses::create');
$routes->post('/admin/connection-statuses/store', 'ConnectionStatuses::store');
$routes->get('/admin/connection-statuses/edit/(:num)', 'ConnectionStatuses::edit/$1');
$routes->post('/admin/connection-statuses/update/(:num)', 'ConnectionStatuses::update/$1');

// Meter Contractors routes (DataTable with AJAX CRUD - no delete option)
$routes->get('/admin/meter-contractors', 'MeterContractors::index');
$routes->get('/admin/meter-contractors/get-table-data', 'MeterContractors::getTableData');
$routes->get('/admin/meter-contractors/create', 'MeterContractors::create');
$routes->post('/admin/meter-contractors/store', 'MeterContractors::store');
$routes->get('/admin/meter-contractors/edit/(:num)', 'MeterContractors::edit/$1');
$routes->post('/admin/meter-contractors/update/(:num)', 'MeterContractors::update/$1');
