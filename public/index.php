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

// Register middleware
// require __DIR__ . '/../src/models/UserModel.php';
// require __DIR__ . '/../src/models/AdminModel.php';


// Register routes

$app->run();