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
     * @ManyToOne(targetEntity="OrderStatus", inversedBy="id")
     */
    protected $status;

    /**
     * @ManyToOne(targetEntity="OrderType", inversedBy="id")
     */
    protected $type;

    /**
     * @Column(type="float")
     */
    protected $price;

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
