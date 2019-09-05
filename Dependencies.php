<?php

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;


$container['entityManager'] = function ($container) {
    $isDevMode = true;
    $config = Setup::createAnnotationMetadataConfiguration(array("src/ORM_models"), $isDevMode);
    
    $conn = array(
        'dbname' => 'postgres',
        'user' => 'postgres',
        'password' => 'GKknYWzq3S',
        'host' => '192.168.1.101',
        // 'host' => '192.168.1.110',
        'driver' => 'pdo_pgsql',
        'port' => '32768'
    );
    
    return EntityManager::create($conn, $config);
};

$container['Auth'] = function ($container) {
    return new Auth($container->get('entityManager'));
};

$container['AuthMiddleware'] = function ($container) {
    return new AuthMiddleware($container->get('Auth'));
};

$container['AdminModel'] = function ($container) {
    return new AdminModel($container->get('entityManager'));
};
$container['CommonModel'] = function ($container) {
    return new CommonModel($container->get('entityManager'));
};
$container['UserModel'] = function ($container) {
    return new UserModel($container->get('entityManager'));
};

$container['AuthController'] = function ($container) {
    return new AuthController(
        $container->get('CommonModel'),
        $container->get('UserModel'),
        $container->get('Auth')
    );
};
$container['ExchangeController'] = function ($container) { // TODO:
    return new ExchangeController(
        $container->get('AdminModel'),
        $container->get('CommonModel'),
        $container->get('UserModel')
    );
};
$container['ItemsController'] = function ($container) { // TODO:
    return new ItemsController(
        $container->get('AdminModel'),
        $container->get('CommonModel'),
        $container->get('UserModel')
    );
};
$container['OrdersController'] = function ($container) { // TODO:
    return new OrdersController(
        $container->get('AdminModel'),
        $container->get('CommonModel'),
        $container->get('UserModel')
    );
};
$container['TopController'] = function ($container) { // TODO:
    return new TopController(        
        $container->get('AdminModel'),
        $container->get('CommonModel'),
        $container->get('UserModel'));
};
$container['UsersController'] = function ($container) {
    return new UsersController(
        $container->get('AdminModel'),
        $container->get('CommonModel'),
        $container->get('UserModel')
    );
};