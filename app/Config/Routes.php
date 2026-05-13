<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/login', 'Home::login');

$routes->group('employer', ['filter' => 'role:employe'], function($routes) {

});

$routes->group('rh', ['filter' => 'role:rh'], function($routes) {
    
});

$routes->group('admin', ['filter' => 'role:admin'], function($routes) {
});