<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/login', 'Home::login');

$routes->group('', ['filter' => 'role:employe'], function($routes) {

});

$routes->group('', ['filter' => 'role:rh'], function($routes) {
    
});

$routes->group('', ['filter' => 'role:admin'], function($routes) {
    
});