<?php

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

$container['notFoundHandler'] = function ($c) {
    return function ($request, $response) use ($c) {
        return $response->withStatus(404)
            ->write(json_encode(['error' => 'Page not found.']));
    };
};

$container['entityManager'] = function ($container) {
    $isDevMode = true;
    $config = Setup::createAnnotationMetadataConfiguration(array("src/ORM_models"), $isDevMode);
    
    $conn = array(
        'dbname' => 'postgres',
        'user' => 'postgres',
        'password' => 'GKknYWzq3S',
        'host' => '192.168.1.102',
        // 'host' => '192.168.1.110',
        'driver' => 'pdo_pgsql',
        'port' => '32768'
    );
    
    return EntityManager::create($conn, $config);
};

$container['repository'] = function ($container){
    return new Repository($container->get('entityManager'));
};

$container['AuthInteractor'] = function ($container) {
    return new AuthInteractor($container->get('repository'));
};

$container['AuthValidator'] = function ($container) {
    return new AuthValidator($container->get('AuthInteractor'));
};

$container['ExchangeInteractor'] = function ($container) {
    return new ExchangeInteractor($container->get('repository'));
};
$container['ItemsInteractor'] = function ($container) {
    return new ItemsInteractor($container->get('repository'));
};
$container['OrdersInteractor'] = function ($container) {
    return new OrdersInteractor($container->get('repository'));
};
$container['TopInteractor'] = function ($container) {
    return new TopInteractor($container->get('repository'));
};
$container['UsersInteractor'] = function ($container) {
    return new UsersInteractor($container->get('repository'));
};

$container['AuthController'] = function ($container) {
    return new AuthController(
        $container->get('AuthInteractor')
    );
};
$container['ExchangeController'] = function ($container) {
    return new ExchangeController(
        $container->get('ExchangeInteractor')
    );
};
$container['ItemsController'] = function ($container) {
    return new ItemsController(
        $container->get('ItemsInteractor')
    );
};
$container['OrdersController'] = function ($container) {
    return new OrdersController(
        $container->get('OrdersInteractor')
    );
};
$container['TopController'] = function ($container) {
    return new TopController(        
        $container->get('TopInteractor')
    );
};
$container['UsersController'] = function ($container) {
    return new UsersController(
        $container->get('UsersInteractor')
    );
};