<?php

class ItemsInteractor{
    private $repository;
    
    public function __construct(Repository $repository){
        $this->repository = $repository;
    }
    
    // Создать тип предмета 
    public function createItemType($name){
        $it = $this->repository->getItemTypeByName($name);

        if (!empty($it)){
            throw new Exception('Item type with name [' . $name . '] already exists.');
        }

        $itemType = new ItemType();
        $itemType->setName($name);

        $this->repository->saveEntity($itemType);
        return true;
    }

    // Получить список всех типов предметов 
    public function getItemTypesList(){
        $itemTypes = $this->repository->getItemTypes();     
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
        $itemType = $this->repository->getItemTypeById($id);
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
        $items = $this->repository->getItems();
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
        $item = $this->repository->getItemById($id);
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
}