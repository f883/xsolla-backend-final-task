<?php

class TopInteractor{
    private $repository;
    
    public function __construct(Repository $repository){
        $this->repository = $repository;
    }

    // Получить самые продаваемые предметы
    public function getTopSallingItems(){
        $items = $this->repository->getItemsSellCountDesc();
        
        $res = [];
        $count = 0;
        foreach($items as $item){
            if ($count > 9){
                break;
            }
            $res[] = [
                'id' => $item->getId(),
                'owner' => $item->getOwner()->getId(),
                'sell count' => $item->getSellCount()
            ];
            $count++;
        }
        return $res;
    } 

    // Получить топ пользователей по количеству денег или по количеству предметов
    public function getTopUsers($filter){
        $res = [];

        switch ($filter){
            case 'money':
                $users = getUsersByBalanceDesc();
                foreach($users as $user){
                    $userId = $user->getId();
                    $res[$userId] = $user->getBalance();
                }
                break;
            case 'items':
                $users = $this->repository->getUsers();
                $items = $this->repository->getItems();
                $res = [];
                foreach($users as $user){
                    $userId = $user->getId();
                    $count = 0;
                    foreach ($items as $item){
                        if ($item->getOwner()->getId() == $userId){
                            $count++;
                        }
                    }
                    $res[$userId] = $count;
                }
                arsort($res);
                break;
            }
        return $res;
    } 
}