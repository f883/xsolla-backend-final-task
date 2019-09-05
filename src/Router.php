<?php

use \Firebase\JWT\JWT;

class Router{
    public function __construct(){}
    
    public function commit($app){
        
        // Auth middleware
        $app->add('AuthMiddleware:dispatch');

        // Main API
        $app->group('/api', function () use ($app) {

            // Authentification
            $app->group('/auth', function () use ($app) {
                $app->post('/register', 'AuthController:register');
                $app->post('/login', 'AuthController:login');
                $app->post('/logout', 'AuthController:logout');
                $app->post('/updatetoken', 'AuthController:updateToken');
            });
            
            // Exchange
            $app->group('/exchange', function () use ($app) {
                $app->get('', 'ExchangeController:getStatus');
                $app->post('/fee', 'ExchangeController:setFee');
                $app->get('/balance', 'ExchangeController:getBalance');
                $app->get('/earn', 'ExchangeController:getEarn');
                $app->put('/deposit/user/{id}', 'ExchangeController:depositMoney');
                $app->put('/withdraw/user/{id}', 'ExchangeController:withdrawMoney');
            });

            // Items
            $app->group('/items', function () use ($app) {
                $app->get('/types', 'ItemsController:getItemTypes');
                $app->get('/types/{id}', 'ItemsController:getItemType');
                $app->get('/', 'ItemsController:getItems');
                $app->get('/{id}', 'ItemsController:getItem');
                $app->post('/types', 'ItemsController:addItemType');
            });

            // Orders
            $app->group('/orders', function () use ($app) {
                $app->get('/buy', 'OrdersController:getOrdersToBuy');
                $app->post('/buy', 'OrdersController:postOrderToBuy');
                $app->get('/sell', 'OrdersController:getOrdersToSell');
                $app->post('/sell', 'OrdersController:postOrderToSell');
                $app->put('/{id}', 'OrdersController:updateOrder');
                $app->delete('/{id}', 'OrdersController:cancelOrder');
            });

            // Top items/orders/etc
            $app->group('/top', function () use ($app) {
                $app->get('/items', 'TopController:getTopItems');
                $app->get('/users', 'TopController:getTopUsers');
            });

            // Users
            $app->group('/users', function () use ($app) {
                $app->get('/{id}', 'UsersController:getUser');
                $app->post('/{id}/items', 'UsersController:addItemToUser');
                $app->get('/{id}/history', 'UsersController:getUserHistory');
            });
        });
    }
}