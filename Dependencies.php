<?php

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

$dotenv = Dotenv\Dotenv::create(__DIR__);
$dotenv->load();

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
        'dbname' => getenv('DB_NAME'),
        'user' => getenv('DB_USER'),
        'password' => getenv('DB_PASSWORD'),
        'host' => getenv('DB_HOST'),
        'driver' => 'pdo_pgsql',
        'port' => getenv('DB_PORT')
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

$container['AuthPresenter'] = function ($container) {
    return new AuthPresenter(
        $container->get('AuthInteractor')
    );
};
$container['ExchangePresenter'] = function ($container) {
    return new ExchangePresenter(
        $container->get('ExchangeInteractor')
    );
};
$container['ItemsPresenter'] = function ($container) {
    return new ItemsPresenter(
        $container->get('ItemsInteractor')
    );
};
$container['OrdersPresenter'] = function ($container) {
    return new OrdersPresenter(
        $container->get('OrdersInteractor')
    );
};
$container['TopPresenter'] = function ($container) {
    return new TopPresenter(        
        $container->get('TopInteractor')
    );
};
$container['UsersPresenter'] = function ($container) {
    return new UsersPresenter(
        $container->get('UsersInteractor')
    );
};