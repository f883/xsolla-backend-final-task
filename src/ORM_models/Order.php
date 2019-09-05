<?php
// Order.php


/**
 * @Entity @Table(name="orders")
 */
class Order
{
    /**
     * @Id @GeneratedValue @Column(type="integer") 
     */
    protected $id;
    
    /**
     * @Column(type="datetime")
     */
    protected $created;

    /**
     * @Column(type="datetime", nullable=true)
     */
    protected $closed;

    /**
     * @Column(type="string")
     */
    protected $status;

    /**
     * @Column(type="float")
     */
    protected $price;

    /**
     * @Column(type="string")
     */
    protected $type;

    /**
     * @ManyToOne(targetEntity="Item", inversedBy="orders")
     */
    protected $item;

    /**
     * @ManyToOne(targetEntity="User", inversedBy="ordersForSale")
     */
    protected $owner;
    
    /**
     * @ManyToOne(targetEntity="User", inversedBy="ordersForBuy")
     */
    protected $secondMember;
    
    public function __construct()
    {
    
    }

    public function getId()
    {
        return $this->id;
    }
    public function setCreated(DateTime $created)
    {
        $this->created = $created;
    }
    public function getCreated()
    {
        return $this->created;
    }
    public function setItem($item)
    {
        $this->item = $item;
    }
    public function getItem()
    {
        return $this->item;
    }
    public function setOwner($owner)
    {
        $this->owner = $owner;
    }
    public function getOwner()
    {
        return $this->owner;
    }
    public function setStatus($status)
    {
        $this->status = $status;
    }
    public function getStatus()
    {
        return $this->status;
    }

    public function setType($type)
    {
        $this->type = $type;
    }
    public function getType()
    {
        return $this->type;
    }

    public function setPrice($price)
    {
        $this->price = $price;
    }
    public function getPrice()
    {
        return $this->price;
    }
}
