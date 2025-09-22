<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->options('api/(:any)', function($path){
    return service('response')
        ->setStatusCode(200)
        ->setHeader('Access-Control-Allow-Origin', '*') // ou seu domínio permitido
        ->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With')
        ->send();
});

$routes->group('api', function($routes) {

    // ROTAS DE USUÁRIO E ACCOUNT // 
    $routes->resource('user', ['controller' => 'UserController', 'filter' => 'auth']);
    $routes->post('user/login', 'UserController::login');
    // ROTAS DE USUÁRIO E ACCOUNT // 

    // ROTAS DE PRODUTOS //
    $routes->resource('product', ['controller' => 'ProductController', 'filter' => 'auth']);
    // ROTAS DE PRODUTOS //
});

