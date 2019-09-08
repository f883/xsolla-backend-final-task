<?php
// User.php


use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity @Table(name="exchange")
 */
class Exchange
{
    /**
     * @Id @GeneratedValue @Column(type="integer")
     * @var string
     */
    protected $id;
    
    /**
     * @Column(type="float")
     * @var float
     */
    protected $fee;

    /**
     * @Column(type="float")
     * @var float
     */
    protected $balance = 0;

    public function __construct()
    {
    }
    public function getId()
    {
        return $this->id;
    }
    public function getFee()
    {
        return $this->fee;
    }
    public function setFee($fee)
    {
        $this->fee = $fee;
    }
    public function getBalance()
    {
        return $this->balance;
    }
    public function setBalance($balance)
    {
        $this->balance = $balance;
    }
}