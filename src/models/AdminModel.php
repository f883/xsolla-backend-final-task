<?php

class AdminModel{
    private $entityManager = null;

    public function __construct(Doctrine\ORM\EntityManager $entityManager){
        $this->entityManager = $entityManager;
    }
    
    // Пополнить баланс пользователя
    public function depositMoney($userId, $value){
        $user = $this->entityManager->getRepository('User')
        ->findOneBy(
           ['id' => $userId]
        );

        if (empty($user)){
            throw new Exception('User not found.');
        }

        $balance = $user->getBalance();
        $user->setBalance($balance + $value);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        return true;
    }

    // Списать с баланса пользователя
    public function withdrawMoney($userId, $value){
        $user = $this->entityManager->getRepository('User')
        ->findOneBy(
           ['id' => $userId]
        );

        if (empty($user)){
            throw new Exception('User not found.');
        }

        $balance = $user->getBalance();
        if ($balance < $value){
            throw new Exception('User has not enough money.');
        }

        $user->setBalance($balance - $value);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        return true;
    } 

    // Создать тип предмета 
    public function createItemType($name){
        $it = $this->entityManager->getRepository('ItemType')
        ->findOneBy(
           ['name' => $name]
        );

        if (!empty($it)){
            throw new Exception('Item type with name [' . $name . '] already exists.');
        }

        $itemType = new ItemType();
        $itemType->setName($name);

        $this->entityManager->persist($itemType);
        $this->entityManager->flush();

        return true;
    }
    
    // Начислить предмет пользователю
    public function depositItem($userId, $itemTypeId){
        $user = $this->entityManager->getRepository('User')
        ->findOneBy(
           ['id' => $userId]
        );

        if (empty($user)){
            throw new Exception('User not found.');
        }
        
        $itemType = $this->entityManager->getRepository('ItemType')
        ->findOneBy(
           ['id' => $itemTypeId]
        );

        if (empty($itemType)){
            throw new Exception('Item type not found.');
        }

        $item = new Item($itemType);
        $item->setOwner($user);
        $this->entityManager->persist($item);
        $this->entityManager->flush();
        return true;
    }

    // Изменить комиссию на торговой площадке
    public function setExchangeFee($value){
        $exchange = $this->entityManager->getRepository('Exchange')
        ->findOneBy(
            [],
            ['id' => 'ASC']
        );

        $exchange->setFee($value);
        $this->entityManager->persist($exchange);
        $this->entityManager->flush();
        return true;
    } 

    // Получить баланс торговой площадки
    public function getExchangeBalance(){
        $exchange = $this->entityManager->getRepository('Exchange')
        ->findOneBy(
            [],
            ['id' => 'ASC']
        );
        return $exchange->getBalance();
    } 

    // Получить выручку за определённый период
    public function getEarn($fromDate, $toDate){
        if ($toDate < $fromDate){
            throw new Exception('ToDate is less than FromDate');
        }

        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('log')
            ->from('OrderLog','log')
            ->add('where', $qb->expr()->between(
                        'log.date',
                        ':from',
                        ':to'
                    )
                )
            ->setParameters([
                    'from' => $fromDate->format('d-M-Y'), 
                    'to' => $toDate->format('d-M-Y')
                ]);
        $logs = $qb->getQuery()->getResult();


        $sum = 0;
        foreach($logs as $log){
            $sum += $log->getExchangeEarn();
        }

        return $sum;
    } 
}