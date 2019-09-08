<?php
// User.php


use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity @Table(name="order_logs")
 */
class OrderLog
{
    /**
     * @Id @GeneratedValue @Column(type="integer")
     * @var string
     */
    protected $id;
    
    /**
     * @Column(type="datetime")
     * @var DateTime
     */
    protected $date;

    /**
     * @Column(type="integer")
     * @var integer
     */
    protected $order;

    /**
     * @Column(type="float")
     * @var float
     */
    protected $exchangeEarn;

    public function __construct()
    {

    }
    public function getId()
    {
        return $this->id;
    }

    public function getDate()
    {
        return $this->date;
    }
    public function setDate($value)
    {
        $this->date = $value;
    }
    public function getOrder()
    {
        return $this->order;
    }
    public function setOrder($value)
    {
        $this->order = $value;
    }
    public function getExchangeEarn()
    {
        return $this->exchangeEarn;
    }
    public function setExchangeEarn($ee)
    {
        $this->exchangeEarn = $ee;
    }
}