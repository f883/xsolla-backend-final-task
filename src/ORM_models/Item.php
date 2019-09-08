<?php
// Item.php
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity @Table(name="items")
 */
class Item
{
    /** 
     * @Id @GeneratedValue @Column(type="integer") 
    **/
    protected $id;

    /** 
     * @Column(type="integer") 
    **/
    protected $sellCount = 0;

    /**
     * @Column(type="datetime", nullable=true)
     */
    protected $lastSaleDate;

    /** 
     * @Column(type="string", nullable=true) 
    **/
    protected $description;

    /**
     * @OneToMany(targetEntity="Order", mappedBy="item")
    **/
    protected $orders;

    /**
     * @ManyToOne(targetEntity="User", inversedBy="items")
    **/
    protected $owner;
    
    /**
     * @ManyToOne(targetEntity="ItemType", inversedBy="id")
    **/
    protected $type;

    public function __construct(ItemType $type){
        $this->type = $type;
        $this->orders = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }
    public function incrementSellCount()
    {
        $this->sellCount++;
    }
    public function getSellCount()
    {
        return $this->sellCount;
    }
    public function setSellCount($sc)
    {
        $this->sellCount = $sc;
    }
    public function setDescription($description)
    {
        $this->description = $description;
    }
    public function getDescription()
    {
        return $this->description;
    }
    public function setOwner($user){
        $this->owner = $user;
    }
    public function getOwner(){
        return $this->owner;
    }
    public function getType(){
        return $this->type;
    }
}