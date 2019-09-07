<?php

class ExchangeInteractor{
    private $repository;
    
    public function __construct(Repository $repository){
        $this->repository = $repository;
    }

    // Получить выручку биржи за определённый период
    public function getEarn($fromDate, $toDate){
        if ($toDate < $fromDate){
            throw new Exception('ToDate is less than FromDate');
        }

        $logs = $this->repository->getLogs($fromDate, $toDate);

        $sum = 0;
        foreach($logs as $log){
            $sum += $log->getExchangeEarn();
        }

        return $sum;
    } 
    
    // Изменить комиссию на торговой площадке
    public function setExchangeFee($value){
        $exchange = $this->repository->getExchange();
        $exchange->setFee($value);
        $this->repository->saveEntity($exchange);
        return true;
    } 

    // Начислить предмет пользователю
    public function depositItem($userId, $itemTypeId){
        $user = $this->getUserById($userId);

        if (empty($user)){
            throw new Exception('User not found.');
        }
        
        $itemType = $this->repository->getItemTypeById($itemTypeId);

        if (empty($itemType)){
            throw new Exception('Item type not found.');
        }

        $item = new Item($itemType);
        $item->setOwner($user);
        $this->repository->saveEntity($item);
        return true;
    }

    // Получить баланс торговой площадки
    public function getExchangeBalance(){
        $exchange = $this->repository->getExchange();
        return $exchange->getBalance();
    } 

    // Пополнить баланс пользователя
    public function depositMoney($userId, $value){
        $user = $this->repository->getUserById($userId);

        if (empty($user)){
            throw new Exception('User not found.');
        }

        $balance = $user->getBalance();
        $user->setBalance($balance + $value);
        
        $this->repository->saveEntity($user);
        return true;
    }

    // Списать с баланса пользователя
    public function withdrawMoney($userId, $value){
        $user = $this->repository->getUserById($userId);

        if (empty($user)){
            throw new Exception('User not found.');
        }

        $balance = $user->getBalance();
        if ($balance < $value){
            throw new Exception('User has not enough money.');
        }

        $user->setBalance($balance - $value);
        $this->repository->saveEntity($user);
        return true;
    } 

    // Получить текущее состояние биржи 
    // (комиссия, количество предметов, количество ордеров)
    public function getExchangeStatus(){
        $fee = $this->repository->getExchange->getFee();
        $usersCount = $this->repository->getUsersCount();
        $ordersCount = $this->repository->getOrdersCount();
        
        $res = [
            'fee' => $fee,
            'users_count' => $usersCount,
            'orders_count' => $ordersCount
        ];
        return $res;
    }
}