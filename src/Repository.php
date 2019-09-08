<?php

use Doctrine\ORM\Query\ResultSetMapping;

class Repository{
    private $entityManager;

    public function __construct($em){
        $this->entityManager = $em;
    }

    public function saveEntity($obj){
        $this->entityManager->persist($obj);
        $this->entityManager->flush();
    }
    public function getUserById($id){
        return $this->entityManager->getRepository('User')
        ->findOneBy(
            ['id' => $id]
        );
    }
    public function getOrdersByOwnerId($id){
        return $this->entityManager->getRepository('Order')
        ->findBy(
            ['owner' => $id]
        );
    }
    public function getUserByAccessToken($tokenHash){
        return $this->entityManager->getRepository('User')
        ->findOneBy(
            ['accessTokenHash' => $tokenHash]
        );
    }
    public function getUserByRefreshToken($tokenHash){
        return $this->entityManager->getRepository('User')
        ->findOneBy(
            ['refreshTokenHash' => $tokenHash]
        );
    }
    public function getUsersCount(){
        $query = $this->entityManager->createQuery('SELECT count(u.id) as users_count FROM User u');
        $users = $query->getResult()[0];
        return $users['users_count'];
    }
    public function getOrdersCount(){
        return count($this->entityManager->getRepository('Order')
        ->findBy(
            [],
            []
        ));
    }
    public function getLogs($fromDate, $toDate){
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
        return $qb->getQuery()->getResult();
    }
    public function getExchange(){
        return $this->entityManager->getRepository('Exchange')
                ->findOneBy(
                    [],
                    ['id' => 'ASC']
                );
    }
    public function getItemTypeById($itemTypeId){
        return $this->entityManager->getRepository('ItemType')
        ->findOneBy(
            ['id' => $itemTypeId]
        );
    }
    public function getItemById($itemId){
        return $this->entityManager->getRepository('Item')
        ->findOneBy(
            ['id' => $itemId]
        );
    }
    public function getItemTypeByName($name){
        return $this->entityManager->getRepository('ItemType')
        ->findOneBy(
            ['name' => $name]
        );
    }
    public function getItemTypes(){
        return $this->entityManager->getRepository('ItemType')
        ->findBy(
            [],
            ['id' => 'ASC']
        );
    }
    public function getItems(){
        return $this->entityManager->getRepository('Item')
        ->findBy(
            [],
            ['id' => 'ASC']
        );
    }
    public function getItemsSellCountDesc(){
        return $this->entityManager->getRepository('Item')
        ->findBy(
            [],
            ['sellCount' => 'DESC']
        );
    }
    public function getUsersByBalanceDesc(){
        return $this->entityManager->getRepository('User')
        ->findBy(
            [],
            ['balance' => 'DESC']
        );
    }
    public function getUsers(){
        return $this->entityManager->getRepository('User')
        ->findBy(
            [],
            []
        );
    }
    public function getOrdersByType($typeValue){
        $type = $this->getOrderTypeByNameCreateIfNotExists($typeValue);

        return $this->entityManager->getRepository('Order')
        ->findBy(
            ['type' => $type->getId()]
        );
    }
    public function getOrdersByTypeWithSort($typeValue, $sortBy){
        $type = $this->entityManager->getRepository('OrderType')
        ->findOneBy(
            ['value' => $typeValue]
        );

        return $this->entityManager->getRepository('Order')
        ->findBy(
            ['type' => $type->getId()],
            [$sortBy => 'ASC']
        );
    }
    public function getOrderById($id){
        return $this->entityManager->getRepository('Order')
        ->findOneBy(
            ['id' => $id]
        );
    }
    public function getOrderByTypeAndItemId($typeDescription, $itemId){
        $type = $this->getOrderTypeByNameCreateIfNotExists($typeDescription);
        
        return $this->entityManager->getRepository('Order')
        ->findOneBy(
           ['item' => $itemId, 'type' => $type]
        );
    }
    public function getOrderBySecondMember($secondMemberId){
        return $this->entityManager->getRepository('Order')
        ->findBy(
            ['secondMember' => $secondMemberId]
        );
    }
    public function getUserByName($name){
        return $this->entityManager->getRepository('User')
        ->findOneBy(
            ['name' => $name]
        );
    }
    public function getOrderStatusByNameCreateIfNotExists($status){
        $os = $this->entityManager->getRepository('OrderStatus')
        ->findOneBy(
            ['value' => $status]
        );

        if (empty($os)){
            $os = new OrderStatus();
            $os->setValue($status);
            $this->saveEntity($os);
        }
        return $os;
    }
    public function getOrderTypeByNameCreateIfNotExists($type){
        $tp = $this->entityManager->getRepository('OrderType')
        ->findOneBy(
            ['value' => $type]
        );

        if (empty($tp)){
            $tp = new OrderType();
            $tp->setValue($type);
            $this->saveEntity($tp);
        }
        return $tp;
    }
    public function getOrCreateUserRoleByValue($value){
        $ur = $this->entityManager->getRepository('UserRole')
        ->findOneBy(
            ['role' => $value]
        );

        if (empty($ur)){
            $ur = new UserRole();
            $ur->setRole($value);
            $this->saveEntity($ur);
        }
        return $ur;
    }
}