<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->group('api', function($routes) {
    $routes->resource('user', ['controller' => 'UserController', 'filter' => 'auth']);
    $routes->post('user/login', 'UserController::login');
});

