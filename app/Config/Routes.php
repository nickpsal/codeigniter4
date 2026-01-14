<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'UserController::get_users');
$routes->get('/users', 'UserController::get_users');
$routes->get('/user/edit/(:num)', 'UserController::update_user/$1');
$routes->post('/user/edit/(:num)', 'UserController::update_user/$1');
$routes->delete('/user/delete/(:num)', 'UserController::delete_user/$1');
$routes->get('/create_user', 'UserController::create_user');
$routes->post('/create_user', 'UserController::create_user');

service('auth')->routes($routes);
