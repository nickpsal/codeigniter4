<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::get_users');
$routes->get('/users', 'Home::get_users');
$routes->get('/user/id/(:num)', 'Home::get_user_by_id/$1');
$routes->get('/user/username/(:alphanum)', 'Home::get_user_by_username/$1');
$routes->get('/user/edit/(:num)', 'Home::update_user/$1');
$routes->post('/user/edit/(:num)', 'Home::update_user/$1');
$routes->delete('/user/delete/(:num)', 'Home::delete_user/$1');
$routes->get('/create_user', 'Home::create_user');
$routes->post('/create_user', 'Home::create_user');
