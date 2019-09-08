<?php

use \Firebase\JWT\JWT;

class Router{
    public function __construct(){}
    
    public function commit($app){
        
        // Auth middleware
        $app->add('AuthValidator:dispatch');

        // Main API
        $app->group('/api', function () use ($app) {

            // Authentification
            $app->group('/auth', function () use ($app) {
                $app->post('/register', 'AuthController:register'); // TODO: tests
                $app->post('/login', 'AuthController:login'); // TODO: tests
                $app->post('/logout', 'AuthController:logout'); // TODO: tests
                $app->post('/updatetoken', 'AuthController:updateToken'); // TODO: tests
            });
            
            // Exchange
            $app->group('/exchange', function () use ($app) {
                $app->get('', 'ExchangeController:getStatus'); // TODO: tests
                $app->post('/fee', 'ExchangeController:setFee'); // TODO: tests
                $app->get('/balance', 'ExchangeController:getBalance'); // TODO: tests
                $app->get('/earn', 'ExchangeController:getEarn'); // TODO: tests
                $app->put('/deposit/user/{id}', 'ExchangeController:depositMoney'); // TODO: tests
                $app->put('/withdraw/user/{id}', 'ExchangeController:withdrawMoney'); // TODO: tests
            });

            // Items
            $app->group('/items', function () use ($app) {
                $app->get('/types', 'ItemsController:getItemTypes'); // TODO: tests
                $app->get('/types/{id}', 'ItemsController:getItemType'); // TODO: tests
                $app->get('/', 'ItemsController:getItems'); // TODO: tests
                $app->get('/{id}', 'ItemsController:getItem'); // TODO: tests
                $app->post('/types', 'ItemsController:addItemType'); // TODO: tests
            });

            // Orders
            $app->group('/orders', function () use ($app) {
                $app->get('/buy', 'OrdersController:getOrdersToBuy'); // TODO: tests
                $app->post('/buy', 'OrdersController:postOrderToBuy'); // TODO: tests
                $app->get('/sell', 'OrdersController:getOrdersToSell'); // TODO: tests
                $app->post('/sell', 'OrdersController:postOrderToSell'); // TODO: tests
                $app->put('/{id}', 'OrdersController:updateOrder'); // TODO: tests
                $app->delete('/{id}', 'OrdersController:cancelOrder'); // TODO: tests
            });

            // Top items/orders/etc
            $app->group('/top', function () use ($app) {
                $app->get('/items', 'TopController:getTopItems'); // TODO: tests
                $app->get('/users', 'TopController:getTopUsers'); // TODO: tests
            });

            // Users
            $app->group('/users', function () use ($app) {
                $app->get('/{id}', 'UsersController:getUser'); // TODO: tests
                $app->post('/{id}/items', 'UsersController:addItemToUser'); // TODO: tests
                $app->get('/{id}/history', 'UsersController:getUserHistory'); // TODO: tests
            });
        });
    }
}