<?php

// use Doctrine\ORM\Doctrine_Query;
use Doctrine\ORM\Query\ResultSetMapping;

class CommonModel{
    private $entityManager = null;

    public function __construct(Doctrine\ORM\EntityManager $entityManager){
        $this->entityManager = $entityManager;
    }

    // Войти в стистему
    public function login($username, $password){
        $hash = password_hash($password, PASSWORD_BCRYPT);

        $user = $this->entityManager->getRepository('User')
        ->findOneBy(
           ['name' => $username]
        );
        if (empty($user)){
            throw new Exception('User with name [' . $username . '] not found.');
        }     
        return $user->getId();
    }
    
    // Получить список всех типов предметов 
    public function getItemTypesList(){
        $itemTypes = $this->entityManager->getRepository('ItemType')
        ->findBy(
            [],
            ['id' => 'ASC']
        );

        $res = [];

        foreach($itemTypes as $it){
            $res[] = [
                'id' => $it->getId(),
                'name' => $it->getName()
            ];
        }
        return $res;
    }

    // Получить информацию о типе предмета
    public function getItemType($id){
        $itemType = $this->entityManager->getRepository('ItemType')
        ->findOneBy(
            ['id' => $id],
            []
        );

        if (empty($itemType)){
            throw new Exception('Item not found.');
        }

        $res = [
            'id' => $itemType->getId(),
            'name' => $itemType->getName()
        ];
        return $res;
    }
    
    // Получить список всех предметов 
    public function getItemsList(){
        $items = $this->entityManager->getRepository('Item')
        ->findBy(
            [],
            ['id' => 'ASC']
        );

        $res = [];

        foreach($items as $item){
            $res[] = [
                'id' => $item->getId(),
                'description' => $item->getDescription(),
                'type_id' => $item->getType()->getId(),
                'owner_id' => $item->getOwner()->getId(),
            ];
        }
        return $res;
    }

    // Получить информацию о предмете
    public function getItem($id){
        $item = $this->entityManager->getRepository('Item')
        ->findOneBy(
            ['id' => $id],
            []
        );

        if (empty($item)){
            throw new Exception('Item not found.');
        }

        $res = [
            'id' => $item->getId(),
            'description' => $item->getDescription(),
            'type_id' => $item->getType()->getId(),
            'owner_id' => $item->getOwner()->getId(),
        ];
        return $res;
    }

    // Получить текущее состояние биржи 
    // (комиссия, количество предметов, количество ордеров)
    public function getExchangeStatus(){
        $fee = $this->entityManager->getRepository('Exchange')
            ->findOneBy(
                [],
                ['id' => 'ASC']
            )->getFee();

        // $usersCount = $this->entityManager->getRepository('User')
        //     ->createQueryBuilder('u')
        //     ->select('count(u.id)')
        //     ->getQuery()
        //     ->getSingleScalarResult();

        $query = $this->entityManager->createQuery('SELECT count(u.id) as users_count FROM User u');
        $users = $query->getResult()[0];
        $usersCount = $users['users_count'];        

        // FIXME: глина
        $ordersCount = count($this->entityManager->getRepository('Order')
            ->findBy(
                [],
                []
            ));

        // $query = $this->entityManager->createQuery('SELECT count(u.id) as orders_count FROM Order u');
        // $users = $query->getResult()[0];
        // $ordersCount = $users['orders_count'];       
        
        $res = [
            'fee' => $fee,
            'users_count' => $usersCount,
            'orders_count' => $ordersCount
        ];
        return $res;
    }

    // Получить самые продаваемые предметы
    public function getTopSallingItems(){
        $items = $this->entityManager->getRepository('Item')
        ->findBy(
            [],
            ['sellCount' => 'DESC']
        );
        
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
                $users = $this->entityManager->getRepository('User')
                ->findBy(
                    [],
                    ['balance' => 'DESC']
                );

                foreach($users as $user){
                    $userId = $user->getId();
                    $res[$userId] = $user->getBalance();
                }
                break;
            case 'items':
                // FIXME: глина
                // потому что groupby не работает

                $users = $this->entityManager->getRepository('User')
                ->findBy(
                    [],
                    []
                );

                $items = $this->entityManager->getRepository('Item')
                ->findBy(
                    [],
                    []
                );

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

    // Получить информацию о пользователе
    public function getUserInfo(string $id){
        $user = $this->entityManager->getRepository('User')
              ->findOneBy(
                 ['id' => $id]
                //  ['id' => 'DESC']
               );

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