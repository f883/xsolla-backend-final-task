<?php

class OrdersInteractor{
    private $repository;
    
    public function __construct(Repository $repository){
        $this->repository = $repository;
    }

    // Купить товар по ордеру
    public function buyItem($userId, $orderId){
        $order = $this->repository->getOrderById($orderId);
        if (empty($order)){
            throw new Exception('Order not found.');          
        }
        if ($order->getType()->getValue() != OrderType::$SELL){
            throw new Exception('Wrong order type.');          
        }
        if ($order->getStatus()->getValue() != OrderStatus::$ACTIVE){
            throw new Exception('Wrong order status.');          
        }

        $item = $order->getItem();

        $nowMinusDay = new DateTime();
        $nowMinusDay = date('Y-m-d', strtotime($nowMinusDay . ' - 1 days'));
        if ($item->getLastSaleDate() > $nowMinusDay){
            throw new Exception('Last buy was less than 24 hours ago.');
        } 

        $seller = $order->getOwner();
        $buyer = $this->repository->getUserById($userId);
        if ($buyer->getBalance() < $order->getPrice()){
            throw new Exception('Buyer has not enough money.');          
        }
        $exchange = $this->repository->getExchange();
        $fee = $order->getPrice()*$exchange->getFee();
        $seller->setBalance($seller->getBalance() + $order->getPrice() - $fee);
        $exchange->setBalance($exchange->getBalance() + $fee);
        $buyer->setBalance($buyer->getBalance() - $order->getPrice());
        $item->setOwner($buyer);
        $order->setStatus($this->repository->getOrderStatusByNameCreateIfNotExists(OrderStatus::$SOLD));
        $order->setClosed(new DataTime());
        $order->setSecondMember($buyer);
        $item->incrementSellCount();

        $log = new OrderLog();
        $log->setDate(new DataTime());
        $log->setExchangeEarn($fee);
        $log->setOrder($order);
        $item->setLastSaleDate(new DateTime());
        $this->repository->saveEntity($log);
        $this->repository->saveEntity($buyer);
        $this->repository->saveEntity($seller);
        $this->repository->saveEntity($order);
        $this->repository->saveEntity($exchange);
    } 

    // Продать товар по ордеру
    public function sellItem($userId, $orderId){
        $order = $this->repository->getOrderById($orderId);
        if (empty($order)){
            throw new Exception('Order not found.');          
        }
        if ($order->getType()->getValue() != OrderType::$BUY){
            throw new Exception('Wrong order type.');          
        }
        if ($order->getStatus()->getValue() != OrderStatus::$ACTIVE){
            throw new Exception('Wrong order status.');          
        }

        $nowMinusDay = new DateTime();
        $nowMinusDay = date('Y-m-d', strtotime($nowMinusDay . ' - 1 days'));
        if ($item->getLastSaleDate() > $nowMinusDay){
            throw new Exception('Last buy was less than 24 hours ago.');
        }

        $item = $order->getItem();
        $buyer = $order->getOwner();
        $seller = $this->repository->getUserById($userId);

        if ($item->getOwner()->getId() != $seller->getId()){
            throw new Exception('Seller is not the owner of the item.');          
        }

        if ($buyer->getBalance() < $order->getPrice()){
            throw new Exception('Buyer has not enough money.');          
        }
        $exchange = $this->repository->getExchange();
        
        $fee = $order->getPrice()*$exchange->getFee();
        $seller->setBalance($seller->getBalance() + $order->getPrice() - $fee);
        $exchange->setBalance($exchange->getBalance() + $fee);

        $buyer->setBalance($buyer->getBalance() - $order->getPrice());
        $item->setOwner($buyer);
        $order->setStatus($this->repository->getOrderStatusByNameCreateIfNotExists(OrderStatus::$SOLD));
        $order->setSecondMember($buyer);
        $order->setClosed(new DataTime());
        $item->incrementSellCount();

        $log = new OrderLog();
        $log->setDate(new DataTime());
        $log->setExchangeEarn($fee);
        $log->setOrder($order);
        $item->setLastSaleDate(new DateTime());
        $this->repository->saveEntity($log);
        $this->repository->saveEntity($buyer);
        $this->repository->saveEntity($seller);
        $this->repository->saveEntity($order);
        $this->repository->saveEntity($exchange);
    } 

    // Создать ордера на продажу
    public function postSellOrder($itemId, $userId, $price){
        $item = $this->repository->getItemById($itemId);
        if (empty($item)){
            throw new Exception('Item not found.');
        }
        $user = $this->repository->getUserById($userId);
        if ($item->getOwner()->getId() !== $user->getId()){
            throw new Exception('User do not own this item.');
        }

        $orderExists = $this->repository->getOrderByTypeAndItemId(OrderType::$SELL, $itemId);
        if (!empty($orderExists)){
            throw new Exception('Order type [SELL] with item [' . $itemId . '] already exists.');
        }

        $order = new Order();
        $order->setCreated(new DateTime());
        $order->setItem($item);
        $order->setPrice($price);
        $order->setOwner($user);
        $order->setStatus($this->repository->getOrderStatusByNameCreateIfNotExists(OrderStatus::$ACTIVE));
        $order->setType($this->repository->getOrderTypeByNameCreateIfNotExists(OrderType::$SELL));
        $this->repository->saveEntity($order);
        return $order->getId();
    }

    // Создать ордера на покупку
    public function postBuyOrder($itemId, $userId, $price){
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
        $order->setStatus($this->repository->getOrderStatusByNameCreateIfNotExists(OrderStatus::$ACTIVE));
        $order->setType($this->repository->getOrderTypeByNameCreateIfNotExists(OrderType::$BUY));
        $this->repository->saveEntity($order);
        return $order->getId();
    } 

    // Отменить ордер
    public function cancellOrder($userId, $orderId){
        $order = $this->repository->getOrderById($orderId);
        if (empty($order)){
            throw new Exception('Order not found.');
        }

        if ($order->getOwner()->getId() != $userId){
            throw new Exception('User have no permissions.');
        }

        $order->setStatus($this->repository->getOrderStatusByNameCreateIfNotExists(OrderStatus::$CANCELLED));
        $this->repository->saveEntity($order);
    } 

    // Обновить ордер
    public function updateOrder($userId, $orderId, $price){
        if (!is_numeric($price)){
            throw new Exception('Price should be a number.');
        }
        $order = $this->repository->getOrderById($orderId);
        if (empty($order)){
            throw new Exception('Order not found.');
        }

        if ($order->getOwner()->getId() != $userId){
            throw new Exception('User have no permissions.');
        }

        $order->setPrice($price);
        $this->repository->saveEntity($order);
    } 

    // Получить список ордеров на покупку (фильтры по предмету, по пользователю)
    public function getSalesList($filter){
        $orders = [];
        switch ($filter){
            case 'users':
                $orders = $this->repository->getOrdersByTypeWithSort(OrderType::$SELL, 'owner');
                break;
            case 'items':
                $orders = $this->repository->getOrdersByTypeWithSort(OrderType::$SELL, 'item');
                break;
            default:
                $orders = $this->repository->getOrdersByType(OrderType::$SELL);
                break;
        }
        return $orders;
    }

    // Получить список ордеров на продажу (фильтры по предмету, по пользователю)
    public function getBuysList($filter){
        $orders = [];
        switch ($filter){
            case 'users':
                $orders = $this->repository->getOrdersByTypeWithSort(OrderType::$BUY, 'owner');
                break;
            case 'items':
                $orders = $this->repository->getOrdersByTypeWithSort(OrderType::$BUY, 'item');
                break;
            default:
                $orders = $this->repository->getOrdersByType(OrderType::$BUY);
                break;
        }
        return $orders;
    } 
}