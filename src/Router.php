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
                $app->post('/register', 'AuthPresenter:register');
                $app->post('/login', 'AuthPresenter:login');  
                $app->post('/logout', 'AuthPresenter:logout');  
                $app->post('/updatetoken', 'AuthPresenter:updateToken');  
            });
            
            // Exchange
            $app->group('/exchange', function () use ($app) {
                $app->get('', 'ExchangePresenter:getStatus');  
                $app->post('/fee', 'ExchangePresenter:setFee');  
                $app->get('/balance', 'ExchangePresenter:getBalance');  
                $app->get('/earn', 'ExchangePresenter:getEarn');  
                $app->put('/deposit/user/{id}', 'ExchangePresenter:depositMoney');  
                $app->put('/withdraw/user/{id}', 'ExchangePresenter:withdrawMoney');  
            });

            // Items
            $app->group('/items', function () use ($app) {
                $app->get('/types', 'ItemsPresenter:getItemTypes');  
                $app->get('/types/{id}', 'ItemsPresenter:getItemType');  
                $app->get('/', 'ItemsPresenter:getItems');  
                $app->get('/{id}', 'ItemsPresenter:getItem');  
                $app->post('/types', 'ItemsPresenter:addItemType');  
            });

            // Orders
            $app->group('/orders', function () use ($app) {
                $app->get('/buy', 'OrdersPresenter:getOrdersToBuy');  
                $app->post('/buy', 'OrdersPresenter:postOrderToBuy');  
                $app->get('/sell', 'OrdersPresenter:getOrdersToSell');  
                $app->post('/sell', 'OrdersPresenter:postOrderToSell');  
                $app->put('/{id}', 'OrdersPresenter:updateOrder');  
                $app->delete('/{id}', 'OrdersPresenter:cancelOrder');  
                $app->post('/{id}/buy', 'OrdersPresenter:buyItem');
                $app->post('/{id}/sell', 'OrdersPresenter:sellItem');
            });

            // Top items/orders/etc
            $app->group('/top', function () use ($app) {
                $app->get('/items', 'TopPresenter:getTopItems');  
                $app->get('/users', 'TopPresenter:getTopUsers');  
            });

            // Users
            $app->group('/users', function () use ($app) {
                $app->get('/{id}', 'UsersPresenter:getUser');  
                $app->post('/{id}/items', 'UsersPresenter:addItemToUser');  
                $app->get('/{id}/history', 'UsersPresenter:getUserHistory');  
            });
        });
    }
}