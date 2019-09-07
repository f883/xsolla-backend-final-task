<?php

class OrdersInteractor{
    private $repository;
    
    public function __construct(Repository $repository){
        $this->repository = $repository;
    }

    // Купить товар по ордеру
    public function buyItem($userId, $orderId){
        // TODO: buyItem

    } 

    // Продать товар по ордеру
    public function sellItem($userId, $orderId){
        // TODO: sellItem

    } 

    // Создать ордера на продажу
    public function postSellOrder($itemId, $userId, $price){
        $item = $this->repository->getItemById($itemId);
        if (empty($item)){
            throw new Exception('Item not found.');
        }
        $user = $this->repository->getUserById($userId);
        $orderExists = $this->repository->getOrderByTypeAndItemId(OrderType::$SELL, $itemId);
        if (!empty($orderExists)){
            throw new Exception('Order type [SELL] with item [' . $itemId . '] already exists.');
        }

        $order = new Order();
        $order->setCreated(new DateTime());
        $order->setItem($item);
        $order->setPrice($price);
        $order->setOwner($user);
        $order->setStatus(UserModel::$ORDER_STATUS_ACTIVE);
        $order->setType(OrderType::$SELL);
        $this->repository->saveEntity($order);
        return $order->getId();
    }

    // Создать ордера на покупку
    public function postBuyOrder($item, $userId, $price){
        $item = $this->repository->getItemById($itemId);
        if (empty($item)){
            throw new Exception('Item not found.');
        }
        $user = $this->repository->getUserById($userId);
        $orderExists = $this->repository->getOrderByTypeAndItemId(OrderType::$BUY, $itemId);
        if (!empty($orderExists)){
            throw new Exception('Order type [BUY] with item [' . $itemId . '] already exists.');
        }

        $order = new Order();
        $order->setCreated(new DateTime());
        $order->setItem($item);
        $order->setPrice($price);
        $order->setOwner($user);
        $order->setStatus(UserModel::$ORDER_STATUS_ACTIVE);
        $order->setType(OrderType::$BUY);
        $this->repository->saveEntity($order);
        return $order->getId();
    } 

    // Отменить ордер
    public function cancellOrder($orderId){
        if (!is_numeric($price)){
            throw new Exception('Price should be a number.');
        }
        $order = $this->repository->getOrderById($orderId);
        if (empty($order)){
            throw new Exception('Order not found.');
        }

        $order->setStatus(UserModel::$ORDER_STATUS_CANCELLED);
        $this->repository->saveEntity($order);
    } 

    // Обновить ордер
    public function updateOrder($orderId, $price){
        if (!is_numeric($price)){
            throw new Exception('Price should be a number.');
        }
        $order = $this->repository->getOrderById($orderId);
        if (empty($order)){
            throw new Exception('Order not found.');
        }

        $order->setPrice($price);
        $this->repository->saveEntity($order);
    } 

    // Получить список ордеров на покупку (фильтры по предмету, по пользователю)
    public function getSalesList($filter){
        // TODO: использование фильтра
        $orders = $this->repository->getOrdersByType(OrderType::$SELL);
        return $orders;
    }

    // Получить список ордеров на продажу (фильтры по предмету, по пользователю)
    public function getBuysList($filter){
        // TODO: использование фильтра
        $orders = $this->repository->getOrdersByType(OrderType::$BUY);
        return $orders;
    } 
}