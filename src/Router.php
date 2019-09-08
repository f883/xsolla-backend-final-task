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
                $app->post('/register', 'AuthPresenter:register'); // TODO: tests
                $app->post('/login', 'AuthPresenter:login'); // TODO: tests
                $app->post('/logout', 'AuthPresenter:logout'); // TODO: tests
                $app->post('/updatetoken', 'AuthPresenter:updateToken'); // TODO: tests
            });
            
            // Exchange
            $app->group('/exchange', function () use ($app) {
                $app->get('', 'ExchangePresenter:getStatus'); // TODO: tests
                $app->post('/fee', 'ExchangePresenter:setFee'); // TODO: tests
                $app->get('/balance', 'ExchangePresenter:getBalance'); // TODO: tests
                $app->get('/earn', 'ExchangePresenter:getEarn'); // TODO: tests
                $app->put('/deposit/user/{id}', 'ExchangePresenter:depositMoney'); // TODO: tests
                $app->put('/withdraw/user/{id}', 'ExchangePresenter:withdrawMoney'); // TODO: tests
            });

            // Items
            $app->group('/items', function () use ($app) {
                $app->get('/types', 'ItemsPresenter:getItemTypes'); // TODO: tests
                $app->get('/types/{id}', 'ItemsPresenter:getItemType'); // TODO: tests
                $app->get('/', 'ItemsPresenter:getItems'); // TODO: tests
                $app->get('/{id}', 'ItemsPresenter:getItem'); // TODO: tests
                $app->post('/types', 'ItemsPresenter:addItemType'); // TODO: tests
            });

            // Orders
            $app->group('/orders', function () use ($app) {
                $app->get('/buy', 'OrdersPresenter:getOrdersToBuy'); // TODO: tests
                $app->post('/buy', 'OrdersPresenter:postOrderToBuy'); // TODO: tests
                $app->get('/sell', 'OrdersPresenter:getOrdersToSell'); // TODO: tests
                $app->post('/sell', 'OrdersPresenter:postOrderToSell'); // TODO: tests
                $app->put('/{id}', 'OrdersPresenter:updateOrder'); // TODO: tests
                $app->delete('/{id}', 'OrdersPresenter:cancelOrder'); // TODO: tests
                $app->post('/{id}/buy', 'OrdersPresenter:buyItem'); // FIXME: tests
                $app->post('/{id}/sell', 'OrdersPresenter:sellItem'); // FIXME: tests
            });

            // Top items/orders/etc
            $app->group('/top', function () use ($app) {
                $app->get('/items', 'TopPresenter:getTopItems'); // TODO: tests
                $app->get('/users', 'TopPresenter:getTopUsers'); // TODO: tests
            });

            // Users
            $app->group('/users', function () use ($app) {
                $app->get('/{id}', 'UsersPresenter:getUser'); // TODO: tests
                $app->post('/{id}/items', 'UsersPresenter:addItemToUser'); // TODO: tests
                $app->get('/{id}/history', 'UsersPresenter:getUserHistory'); // TODO: tests
            });
        });
    }
}