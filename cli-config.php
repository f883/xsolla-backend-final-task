<?php
// cli-config.php

$container = new \Slim\Container();
require __DIR__ . '/Dependencies.php';

return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($container->get('entityManager'));