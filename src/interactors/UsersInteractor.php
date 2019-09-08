<?php

class UsersInteractor{
    private $repository;
    
    public function __construct(Repository $repository){
        $this->repository = $repository;
    }
    
    // Получить историю действий пользователя (фильтры по типу ордера)
    public function getUserHistory($userId, $filter = null){
        $user = $this->repository->getUserById($userId);

        if (empty($user)){
            throw new Exception('User not found.');
        }

        $res = [];
        if (in_array('sell', $filter)){
            $userOrders = $this->repository->getOrdersByOwnerId($userId);

            $tt = [];
            foreach($userOrders as $order){
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
            $userSecondDealMember = $this->repository->getOrderBySecondMember($userId);
            
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

    // Начислить предмет пользователю
    public function depositItem($requesterId, $userId, $itemTypeId){
        $requester = $this->repository->getUserById($requesterId);
        if ($requester->getRole()->getRole() !== UserRole::$ADMIN){
            throw new Exception('User have not enough permissions.');
        }

        $user = $this->repository->getUserById($userId);

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

    public function checkAdminAccess($userId){
        $user = $this->repository->getUserById($userId);
        if ($user->getRole()->getRole() === UserRole::$ADMIN){
            return true;
        }
        else{
            return false;
        }
    }

    // Получить информацию о пользователе
    public function getUserInfo(string $id){
        $user = $this->repository->getUserById($id);

        if (empty($user)){
            throw new Exception('User not found.');
        }
        $res = [
            'name' => $user->getName(),
            'balance' => $user->getBalance(),
            'items' => $user->getItems(),
        ];

        return $res;
    } 
}