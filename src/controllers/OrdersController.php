<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class OrdersController{
    private $ordersInteractor;

    public function __construct(OrdersInteractor $ordersInteractor){
        $this->ordersInteractor = $ordersInteractor;
    }    
    public function getOrdersToBuy(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();
        $res = [];
        $respCode = 200;
        if (empty($data['filter'])){
            $data['filter'] = [];
        }
        $orders = $this->ordersInteractor->getSalesList($data['filter']);
        $res = ['ok' => 'true', 'sell_orders' => $orders];
        $response->getBody()->write(json_encode($res));
        return $response->withStatus($respCode);
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

    public function getOrdersToSell(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();
        $res = [];
        $respCode = 200;
        if (empty($data['filter'])){
            $data['filter'] = [];
        }
        $orders = $this->ordersInteractor->getBuysList($data['filter']);
        $res = ['ok' => 'true', 'buy_orders' => $orders];
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
                    $this->ordersInteractor->updateOrder($args['id'], $price);
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
                $this->ordersInteractor->cancellOrder($args['id']);
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