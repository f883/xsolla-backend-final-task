<?php

// use Slim\Factory\AppFactory;
// use \Slim\App;

require __DIR__ . '/../vendor/autoload.php';

// Set up dependencies
$container = new \Slim\Container();
require __DIR__ . '/../Dependencies.php';

// create app instance
$app = new \Slim\App($container);
$router = new Router();
$router->commit($app);

// TODO: куда-то убрать в настройки
$repos = $container->get('repository');
if (empty($repos->getExchange())){
    $exchange = new Exchange();
    $exchange->setFee(0.05);
    $exchange->setBalance(0);
    $repos->saveEntity($exchange);
}

// Register middleware
// require __DIR__ . '/../src/models/UserModel.php';
// require __DIR__ . '/../src/models/AdminModel.php';


// Register Router

$app->run();