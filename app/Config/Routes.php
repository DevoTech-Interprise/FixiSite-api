<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->group('api', function($routes) {

    // ROTAS DE USUÁRIO E ACCOUNT // 
    $routes->resource('user', ['controller' => 'UserController', 'filter' => 'auth']);
    $routes->post('user/login', 'UserController::login');
    // ROTAS DE USUÁRIO E ACCOUNT // 

    // ROTAS DE PRODUTOS //
    $routes->resource('product', ['controller' => 'ProductController', 'filter' => 'auth']);
    // ROTAS DE PRODUTOS //
});

