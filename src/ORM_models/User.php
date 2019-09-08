<?php
// User.php


use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity @Table(name="users")
 */
class User
{
    /**
     * @Id @GeneratedValue @Column(type="integer")
     * @var integer
     */
    protected $id;
    
    /**
     * @Column(type="string")
     * @var string
     */
    protected $name;

    /**
     * @Column(type="float", nullable=true)
     * @var float
     */
    protected $balance = 0;

    /**
     * @Column(type="string")
     * @var string
     */
    protected $passwordHash;

    /**
     * @Column(type="string", nullable=true)
     * @var string
     */
    protected $accessTokenHash;

    /**
     * @Column(type="string", nullable=true)
     * @var string
     */
    protected $refreshTokenHash;

    /**
     * @Column(type="string", nullable=true)
     * @var string
     */
    protected $tokenSalt;

    /**
     * @ManyToOne(targetEntity="UserRole", inversedBy="id")
     * @var UserRole
     */
    protected $role;

    /**
     * @OneToMany(targetEntity="Order", mappedBy="seller")
     * @var Order[]
     */
    protected $ordersForSale = null;
    
    /**
     * @OneToMany(targetEntity="Order", mappedBy="buyer")
     * @var Order[]
     */
    protected $ordersForBuy = null;

    /**
     * @OneToMany(targetEntity="Item", mappedBy="owner")
     * @var Item[]
     */
    protected $items = null;

    public function __construct()
    {
        $this->ordersForSale = new ArrayCollection();
        $this->ordersForBuy = new ArrayCollection();
        $this->items = new ArrayCollection();
    }
    public function getId()
    {
        return $this->id;
    }
    public function getName()
    {
        return $this->name;
    }
    public function setName($name)
    {
        $this->name = $name;
    }
    public function getBalance()
    {
        return $this->balance;
    }
    public function setBalance($value)
    {
        $this->balance = $value;;
    }
    public function getPasswordHash()
    {
        return $this->passwordHash;
    }
    public function setPasswordHash($ph)
    {
        $this->passwordHash = $ph;
    }

    public function getRole(){
        return $this->role;
    }
    public function setRole($role){
        $this->role = $role;
    }

    public function addItem($item){
        $this->items[] = $item;
    }
    public function removeItem($itemId){
        foreach($this->items as $item){
            if ($item->getId() == $itemId){
                unset($item);
                break;
            }
        }
    }
    public function getItems(){
        return $this->items;
        // return $this->items->toArray();
    }

    public function getAccessTokenHash()
    {
        return $this->accessTokenHash;
    }
    public function setAccessTokenHash($ath)
    {
        $this->accessTokenHash = $ath;
    }

    public function getRefreshTokenHash()
    {
        return $this->refreshTokenHash;
    }
    public function setRefreshTokenHash($rth)
    {
        $this->refreshTokenHash = $rth;
    }

    public function getTokenSalt()
    {
        return $this->tokenSalt;
    }
    public function setTokenSalt($ts)
    {
        $this->tokenSalt = $ts;
    }
}