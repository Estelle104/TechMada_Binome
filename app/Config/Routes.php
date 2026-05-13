<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->post('/login', 'Home::login');
$routes->get('/logout', 'Home::logout');

$routes->group('employer', ['filter' => 'role:employe'], function($routes) {
	$routes->get('/', 'employer\\EmployerController::index');
	$routes->get('conges/nouvelle', 'employer\\EmployerController::create');
	$routes->get('conges/mes', 'employer\\EmployerController::mesConge');
	$routes->get('profil', 'employer\\EmployerController::profil');
	$routes->get('(:num)', 'employer\\EmployerController::profile/$1');
	$routes->post('conges', 'employer\\EmployerController::demandeConge');
	$routes->get('conges', 'employer\\EmployerController::listeConge');
	$routes->post('conges/annuler/(:num)', 'employer\\EmployerController::annulerConge/$1');
});

$routes->group('rh', ['filter' => ['auth', 'role:rh']], function($routes) {
	$routes->get('/', 'respRH\\RespRhController::index');
	$routes->get('approve/(:num)', 'respRH\\RespRhController::approve/$1');
	$routes->post('approve/(:num)', 'respRH\\RespRhController::approve_post/$1');
	$routes->get('reject/(:num)', 'respRH\\RespRhController::reject/$1');
	$routes->post('reject/(:num)', 'respRH\\RespRhController::reject_post/$1');
});

$routes->group('admin', ['filter' => ['auth', 'role:admin']], function($routes) {
	$routes->get('/', 'admin\\AdminController::index');
	$routes->get('employes', 'admin\\AdminController::employes');
	$routes->post('employes/create', 'admin\\AdminController::createEmploye');
	$routes->post('employes/update/(:num)', 'admin\\AdminController::updateEmploye/$1');
	$routes->post('employes/deactivate/(:num)', 'admin\\AdminController::deactivateEmploye/$1');
	$routes->get('departements', 'admin\\AdminController::departements');
	$routes->post('departements/create', 'admin\\AdminController::saveDepartement');
	$routes->post('departements/update/(:num)', 'admin\\AdminController::updateDepartement/$1');
	$routes->post('departements/delete/(:num)', 'admin\\AdminController::deleteDepartement/$1');
	$routes->get('types', 'admin\\AdminController::typesConge');
	$routes->post('types/create', 'admin\\AdminController::saveTypeConge');
	$routes->post('types/update/(:num)', 'admin\\AdminController::updateTypeConge/$1');
	$routes->post('types/delete/(:num)', 'admin\\AdminController::deleteTypeConge/$1');
	$routes->get('soldes', 'admin\\AdminController::soldes');
	$routes->post('soldes/save', 'admin\\AdminController::saveSolde');
	$routes->get('conges', 'admin\\AdminController::conges');
});