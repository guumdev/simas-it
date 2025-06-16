<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->get('dashboard', 'DashboardController::index');

$routes->get('datatables/items', 'ItemController::getItemDt');
$routes->get('datatables/asset-locations', 'AssetLocationController::getAssetLocationDt');
$routes->get('datatables/asset-managers', 'AssetManagerController::getAssetManagerDt');
$routes->get('datatables/asset-categories', 'AssetCategoryController::getAssetCategoryDt');

$routes->get('select/get-asset-managers', 'ItemController::getAssetManagers');

$routes->get('asset-locations/counter', 'AssetLocationController::getAssetLocationCounter');
$routes->get('asset-locations', 'AssetLocationController::index');
$routes->post('asset-locations/create', 'AssetLocationController::create');
$routes->get('asset-locations/show/(:num)', 'AssetLocationController::show/$1');
$routes->post('asset-locations/update/(:num)', 'AssetLocationController::update/$1');
$routes->delete('asset-locations/delete/(:num)', 'AssetLocationController::delete/$1');

$routes->get('asset-managers/counter', 'AssetManagerController::getAssetManagerCounter');
$routes->get('asset-managers', 'AssetManagerController::index');
$routes->post('asset-managers/create', 'AssetManagerController::create');
$routes->get('asset-managers/show/(:num)', 'AssetManagerController::show/$1');
$routes->post('asset-managers/update/(:num)', 'AssetManagerController::update/$1');
$routes->delete('asset-managers/delete/(:num)', 'AssetManagerController::delete/$1');

$routes->get('asset-categories/counter', 'AssetCategoryController::getAssetCategoryCounter');
$routes->get('asset-categories', 'AssetCategoryController::index');
$routes->post('asset-categories/create', 'AssetCategoryController::create');
$routes->get('asset-categories/show/(:num)', 'AssetCategoryController::show/$1');
$routes->post('asset-categories/update/(:num)', 'AssetCategoryController::update/$1');
$routes->delete('asset-categories/delete/(:num)', 'AssetCategoryController::delete/$1');

$routes->get('items/counter', 'ItemController::getItemCounter');
$routes->get('items', 'ItemController::index');
$routes->get('items/create', 'ItemController::create');
$routes->post('items/store', 'ItemController::store');
$routes->get('items/show/(:num)', 'ItemController::show/$1');
$routes->get('items/edit/(:num)', 'ItemController::edit/$1');
$routes->put('items/update/(:num)', 'ItemController::update/$1');
$routes->delete('items/delete/(:num)', 'ItemController::delete/$1');
$routes->get('items/excel/export', 'ItemController::exportExcel');
$routes->post('items/excel/import', 'ItemController::importExcel');
$routes->get('items/excel/template', 'ItemController::downloadTemplate');
