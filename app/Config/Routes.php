<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->post('/login', 'Home::login');
$routes->get('/logout', 'Home::logout');

$routes->group('employer', ['filter' => 'auth,role:employe'], function($routes) {

});

$routes->group('rh', ['filter' => 'auth,role:rh'], function($routes) {
	$routes->get('/', 'respRH\\RespRhController::index');
	$routes->get('approve/(:num)', 'respRH\\RespRhController::approve/$1');
	$routes->post('approve/(:num)', 'respRH\\RespRhController::approve_post/$1');
	$routes->get('reject/(:num)', 'respRH\\RespRhController::reject/$1');
	$routes->post('reject/(:num)', 'respRH\\RespRhController::reject_post/$1');
});

$routes->group('admin', ['filter' => 'auth,role:admin'], function($routes) {
	$routes->get('/', 'admin\\AdminController::index');
});