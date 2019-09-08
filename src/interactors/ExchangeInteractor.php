<?php

class ExchangeInteractor{
    private $repository;
    
    public function __construct(Repository $repository){
        $this->repository = $repository;
    }

    // Получить выручку биржи за определённый период
    public function getEarn($requesterId, $fromDate, $toDate){
        $requester = $this->repository->getUserById($requesterId);
        if ($requester->getRole()->getRole() !== UserRole::$ADMIN){
            throw new Exception('User have not enough permissions.');
        }

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
    public function setExchangeFee($requesterId, $value){
        $requester = $this->repository->getUserById($requesterId);
        if ($requester->getRole()->getRole() !== UserRole::$ADMIN){
            throw new Exception('User have not enough permissions.');
        }

        $exchange = $this->repository->getExchange();
        $exchange->setFee($value);
        $this->repository->saveEntity($exchange);
        return true;
    } 

    // Получить баланс торговой площадки
    public function getExchangeBalance($requesterId){
        $requester = $this->repository->getUserById($requesterId);
        if ($requester->getRole()->getRole() !== UserRole::$ADMIN){
            throw new Exception('User have not enough permissions.');
        }

        $exchange = $this->repository->getExchange();
        return $exchange->getBalance();
    } 

    // Пополнить баланс пользователя
    public function depositMoney($requesterId, $userId, $value){
        $requester = $this->repository->getUserById($requesterId);
        if ($requester->getRole()->getRole() !== UserRole::$ADMIN){
            throw new Exception('User have not enough permissions.');
        }

        $user = $this->repository->getUserById($userId);

        if (empty($user)){
            throw new Exception('User not found.');
        }

        $balance = $user->getBalance();
        $user->setBalance($balance + $value);
        
        $this->repository->saveEntity($user);
        return $user->getBalance();
    }

    // Списать с баланса пользователя
    public function withdrawMoney($requesterId, $userId, $value){
        $requester = $this->repository->getUserById($requesterId);
        if ($requester->getRole()->getRole() !== UserRole::$ADMIN){
            throw new Exception('User have not enough permissions.');
        }

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
        return $user->getBalance();
    } 

    // Получить текущее состояние биржи 
    // (комиссия, количество предметов, количество ордеров)
    public function getExchangeStatus(){
        $fee = $this->repository->getExchange()->getFee();
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