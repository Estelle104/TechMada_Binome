<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/login', 'Home::login');

$routes->group('employer', ['filter' => 'role:employe'], function($routes) {
    $routes->get('/', 'employer/EmployerController::index');
    $routes->get('(:num)', 'employer/EmployerController::profile/$1');
    $routes->post('conges', 'employer/EmployerController::demandeConge');
    $routes->get('conges', 'employer/EmployerController::listeConge');
    $routes->post('conges/annuler/(:num)', 'employer/EmployerController::annulerConge/$1');
});

$routes->group('rh', ['filter' => 'role:rh'], function($routes) {
    
});

$routes->group('admin', ['filter' => 'role:admin'], function($routes) {
});