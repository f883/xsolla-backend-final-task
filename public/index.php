<?php


require __DIR__ . '/../vendor/autoload.php';

$container = new \Slim\Container();
require __DIR__ . '/../Dependencies.php';

$app = new \Slim\App($container);
$router = new Router();
$router->commit($app);


$repos = $container->get('repository');
if (empty($repos->getExchange())){
    $exchange = new Exchange();
    $exchange->setFee(0.05);
    $exchange->setBalance(0);
    $repos->saveEntity($exchange);
}

$app->run();