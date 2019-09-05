<?php

class UserModel{
    private $entityManager = null;

    public static $ORDER_STATUS_ACTIVE = 'ACTIVE';
    public static $ORDER_STATUS_SOLD = 'SOLD';
    public static $ORDER_STATUS_CANCELLED = 'CANCELLED';
    
    public static $ORDER_TYPE_BUY = 'BUY';
    public static $ORDER_TYPE_SELL = 'SELL';


    public function __construct(Doctrine\ORM\EntityManager $entityManager){
        $this->entityManager = $entityManager;
    }

    // Регистрация
    public function register($username, $password){
        $hash = password_hash($password, PASSWORD_BCRYPT);

        $user = $this->entityManager->getRepository('User')
        ->findOneBy(
           ['name' => $username]
        );
        if (!empty($user)){
            throw new Exception('User with name [' . $username . '] already exists.');
        }     

        $user = new User();
        $user->setName($username);
        $user->setPasswordHash($hash);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user->getId();
    } 

    // Получить список ордеров на покупку (фильтры по предмету, по пользователю)
    public function getSalesList($filter){
        // TODO: использование фильтра

        $orders = $this->entityManager->getRepository('Order')
        ->findOneBy(
           ['type' => UserModel::$ORDER_TYPE_SELL]
        );
        return $orders;
    }

    // Получить список ордеров на продажу (фильтры по предмету, по пользователю)
    public function getBuysList($filter){
        // TODO: использование фильтра

        $orders = $this->entityManager->getRepository('Order')
        ->findOneBy(
           ['type' => UserModel::$ORDER_TYPE_BUY]
        );
        return $orders;
    } 

    // Получить подробную информацию о товаре
    public function getItemInfo($itemId){
        // TODO:

    } 

    // Купить товар
    public function buyItem($userId, $orderId){
        // TODO:

    } 

    // Создать ордера на продажу
    public function postSellOrder($itemId, $userId, $price){
        $item = $this->entityManager->getRepository('Item')
        ->findOneBy(
           ['id' => $itemId]
        );
        if (empty($item)){
            throw new Exception('Item not found.');
        }
        $user = $this->entityManager->getRepository('User')
        ->findOneBy(
           ['id' => $userId]
        );

        $orderExists = $this->entityManager->getRepository('Order')
        ->findOneBy(
           ['item' => $itemId, 'type' => UserModel::$ORDER_TYPE_SELL]
        );
        if (!empty($orderExists)){
            throw new Exception('Order type [SELL] with item [' . $itemId . '] already exists.');
        }

        // TODO: добавить поиск среди ордеров на продажу с автоматическим выполнением при нахождении с ценой ниже заданной
        // поиск минимальной цены?

        $order = new Order();
        $order->setCreated(new DateTime());
        $order->setItem($item);
        $order->setPrice($price);
        $order->setOwner($user);
        $order->setStatus(UserModel::$ORDER_STATUS_ACTIVE);
        $order->setType(UserModel::$ORDER_TYPE_SELL);
        $this->entityManager->persist($order);
        $this->entityManager->flush();
        return $order->getId();
    }

    // Создать ордера на покупку
    public function postBuyOrder($item, $userId, $price){
        $item = $this->entityManager->getRepository('Item')
        ->findOneBy(
           ['id' => $itemId]
        );
        if (empty($item)){
            throw new Exception('Item not found.');
        }
        $user = $this->entityManager->getRepository('User')
        ->findOneBy(
           ['id' => $userId]
        );

        $orderExists = $this->entityManager->getRepository('Order')
        ->findOneBy(
           ['item' => $itemId, 'type' => UserModel::$ORDER_TYPE_BUY]
        );
        if (!empty($orderExists)){
            throw new Exception('Order type [BUY] with item [' . $itemId . '] already exists.');
        }

        // TODO: добавить поиск среди ордеров на продажу с автоматическим выполнением при нахождении с ценой ниже заданной
        // поиск минимальной цены?

        $order = new Order();
        $order->setCreated(new DateTime());
        $order->setItem($item);
        $order->setPrice($price);
        $order->setOwner($user);
        $order->setStatus(UserModel::$ORDER_STATUS_ACTIVE);
        $order->setType(UserModel::$ORDER_TYPE_BUY);
        $this->entityManager->persist($order);
        $this->entityManager->flush();
        return $order->getId();
    } 

    // Отменить ордер
    public function cancellOrder($orderId){
        if (!is_numeric($price)){
            throw new Exception('Price should be a number.');
        }

        $order = $this->entityManager->getRepository('Order')
        ->findOneBy(
           ['id' => $orderId]
        );

        if (empty($order)){
            throw new Exception('Order not found.');
        }

        $order->setStatus(UserModel::$ORDER_STATUS_CANCELLED);
        $this->entityManager->persist($order);
        $this->entityManager->flush();
    } 

    // Обновить ордер
    public function updateOrder($orderId, $price){
        if (!is_numeric($price)){
            throw new Exception('Price should be a number.');
        }

        $order = $this->entityManager->getRepository('Order')
        ->findOneBy(
           ['id' => $orderId]
        );

        if (empty($order)){
            throw new Exception('Order not found.');
        }

        $order->setPrice($price);
        $this->entityManager->persist($order);
        $this->entityManager->flush();
    } 

    // Получить историю действий пользователя (фильтры по типу ордера)
    public function getUserHistory($userId, $filter = null){
        // $user = $this->entityManager->getRepository('User')
        // ->findOneBy(
        //    ['id' => 22]
        // );

        // $order = new Order();
        // $order->setOwner($user);
        // $order->setStatus('open');
        // $order->setType('sell');
        // $order->setCreated(new DateTime());
        // $this->entityManager->persist($order);
        // $this->entityManager->flush();


        $user = $this->entityManager->getRepository('User')
        ->findOneBy(
           ['id' => $userId]
        );

        if (empty($user)){
            throw new Exception('User not found.');
        }

        $res = [];
        if (in_array('sell', $filter)){
            $userOwner = $this->entityManager->getRepository('Order')
            ->findBy(
               ['owner' => $userId]
            );

            $tt = [];
            foreach($userOwner as $order){
                $tt[] = [
                    'item' => $order->getItem(),
                    'posted date' => $order->getCreated(),
                    'type' => $order->getType(),
                    'owner' => $order->getOwner(),
                ];
            };
            $res = array_merge($res, $tt);
        }

        if (in_array('buy', $filter)){
            $userSecondDealMember = $this->entityManager->getRepository('Order')
            ->findBy(
                ['secondMember' => $userId]
            );
            
            $tt = [];
            foreach($userSecondDealMember as $order){
                $tt[] = [
                    'item' => $order->getItem(),
                    'posted date' => $order->getCreated(),
                    'type' => $order->getType(),
                    'owner' => $order->getOwner(),
                ];
            };
            $res = array_merge($res, $tt);
        }

        return $res;
    } 
}