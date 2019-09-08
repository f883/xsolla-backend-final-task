<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class OrdersPresenter{
    private $ordersInteractor;

    public function __construct(OrdersInteractor $ordersInteractor){
        $this->ordersInteractor = $ordersInteractor;
    }    
    public function getOrdersToSell(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();
        $res = [];
        $respCode = 200;
        if (empty($data['filter'])){
            $data['filter'] = [];
        }
        $orders = $this->ordersInteractor->getSalesList($data['filter']);
        $res = ['ok' => 'true', 'sell_orders' => $this->mapOrders($orders)];
        $response->getBody()->write(json_encode($res));
        return $response->withStatus($respCode);
    }
    public function getOrdersToBuy(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();
        $res = [];
        $respCode = 200;
        if (empty($data['filter'])){
            $data['filter'] = [];
        }
        $orders = $this->ordersInteractor->getBuysList($data['filter']);
        $res = ['ok' => 'true', 'buy_orders' => $this->mapOrders($orders)];
        $response->getBody()->write(json_encode($res));
        return $response->withStatus($respCode);
    }

    private function mapOrders($orders){
        $res = [];

        foreach ($orders as $order){
            $res[] = [
                'id' => $order->getId(),
                'owner' => $order->getOwner()->getId(),
                'item' => $order->getItem()->getId(),
                'created' => $order->getCreated(),
                'type' => $order->getType()->getValue()
            ];
        }

        return $res;
    }

    public function postOrderToBuy(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();
        $res = [];
        $respCode = 200;
        
        if (empty($data['item_id'])){
            $res = ['error' => 'Field [item_id] not set.'];
            $respCode = 400;
        }
        else{
            if (empty($data['user_id'])){
                $res = ['error' => 'Field [user_id] not set.'];
                $respCode = 400;
            }
            else{
                if (empty($data['price'])){
                    $res = ['error' => 'Field [price] not set.'];
                $respCode = 400;
            }
                else{
                    try{
                        $orderId = $this->ordersInteractor->postBuyOrder($data['item_id'], $data['user_id'], $data['price']);
                        $res = ['ok' => 'true', 'order_id' => $orderId];
                    }
                    catch(Exception $ex){
                        $res = ['error' => $ex->getMessage()];
                        $respCode = 400;                        
                    }
                }
            }
        }

        $response->getBody()->write(json_encode($res));
        return $response->withStatus($respCode);
    }

    public function postOrderToSell(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();
        $res = [];
        $respCode = 200;
        
        if (empty($data['item_id'])){
            $res = ['error' => 'Field [item_id] not set.'];
            $respCode = 400;
        }
        else{
            if (empty($data['user_id'])){
                $res = ['error' => 'Field [user_id] not set.'];
                $respCode = 400;
            }
            else{
                if (empty($data['price'])){
                    $res = ['error' => 'Field [price] not set.'];
                $respCode = 400;
            }
                else{
                    try{
                        $orderId = $this->ordersInteractor->postSellOrder($data['item_id'], $data['user_id'], $data['price']);
                        $res = ['ok' => 'true', 'order_id' => $orderId];
                    }
                    catch(Exception $ex){
                        $res = ['error' => $ex->getMessage()];
                        $respCode = 400;                        
                    }
                }
            }
        }

        $response->getBody()->write(json_encode($res));
        return $response->withStatus($respCode);
    }

    public function buyItem(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();
        $res = [];
        $respCode = 200;

        if (!$this->checkIntValue($args['id'])){
            $res = ['error' => 'Wrong order id.'];
            $respCode = 400;
        }
        else{
            try{
                $this->ordersInteractor->buyItem($data['user_id'], $args['id']);
                $res = ['ok' => 'true'];
            }
            catch(Exception $ex){
                $res = ['error' => $ex->getMessage()];
                $respCode = 400;
            }
        }

        $response->getBody()->write(json_encode($res));
        return $response->withStatus($respCode);
    }
    public function sellItem(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();
        $res = [];
        $respCode = 200;

        if (!$this->checkIntValue($args['id'])){
            $res = ['error' => 'Wrong order id.'];
            $respCode = 400;
        }
        else{
            try{
                $this->ordersInteractor->sellItem($data['user_id'], $args['id']);
                $res = ['ok' => 'true'];
            }
            catch(Exception $ex){
                $res = ['error' => $ex->getMessage()];
                $respCode = 400;
            }
        }
        
        $response->getBody()->write(json_encode($res));
        return $response->withStatus($respCode);
    }

    public function updateOrder(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();
        $res = [];
        $respCode = 200;
        
        if (!$this->checkIntValue($args['id'])){
            $res = ['error' => 'Wrong order id.'];
            $respCode = 400;
        }
        else{
            $changes = false;
            $price = null;
            if (!empty($data['price'])){
                $price = $data['price'];
                $changes = true;
            }

            if ($changes){
                try
                {
                    $this->ordersInteractor->updateOrder($data['user_id'], $args['id'], $price);
                    $res = ['ok' => 'true'];
                }
                catch(Exception $ex){                
                    $res = ['error' => $ex->getMessage()];
                    $respCode = 400;
                }
            }
            else{
                $res = ['ok' => 'false', 'message' => 'Nothing to change.'];
            }
        }

        $response->getBody()->write(json_encode($res));
        return $response->withStatus($respCode);
    }

    public function cancelOrder(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();
        $res = [];
        $respCode = 200;

        if (!$this->checkIntValue($args['id'])){
            $res = ['error' => 'Wrong order id.'];
            $respCode = 400;
        }
        else{
            try{
                $this->ordersInteractor->cancellOrder($data['user_id'], $args['id']);
                $res = ['ok' => 'true'];
            }
            catch(Exception $ex){                
                $res = ['error' => $ex->getMessage()];
                $respCode = 400;
            }
        }
        $response->getBody()->write(json_encode($res));
        return $response->withStatus($respCode);
    }

    private function checkIntValue($value){
        if (!is_numeric($value)){
            return false;
        }
        else{
            if ((int)$value < 0){
                return false;
            }
        }
        return true;
    }
}