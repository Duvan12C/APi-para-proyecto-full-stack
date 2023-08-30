<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->group('productos', ['namespace' => 'App\Controllers'], function ($routes) {
    $routes->get('/', 'Productos::index');
    $routes->post('store', 'Productos::store');
    $routes->put('update/(:num)', 'Productos::update/$1');
    $routes->delete('delete/(:num)', 'Productos::delete/$1');
    $routes->get('get/(:num)', 'Productos::getProducto/$1');
});

$routes->group('inventario', ['namespace' => 'App\Controllers'], function ($routes) {
    $routes->get('/', 'Inventario::index');
    $routes->post('store', 'Inventario::store');
    $routes->put('update/(:num)', 'Inventario::update/$1');
    $routes->delete('delete/(:num)', 'Inventario::delete/$1');
    $routes->get('get/(:num)', 'Inventario::getInventario/$1');
});




$routes->group('movimientos', ['namespace' => 'App\Controllers'], function ($routes) {
    $routes->get('', 'MovimientosInventario::index');
    $routes->post('store', 'MovimientosInventario::store');
    $routes->put('update/(:num)', 'MovimientosInventario::update/$1');
    $routes->delete('delete/(:num)', 'MovimientosInventario::delete/$1');
    $routes->get('get/(:num)', 'MovimientosInventario::getMovimiento/$1');
});
