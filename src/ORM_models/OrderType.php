<?php
// ItemType.php
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity @Table(name="order_types")
 */
class OrderType
{
    public static $BUY = 'BUY';
    public static $SELL = 'SELL';

    /** 
     * @Id @GeneratedValue @Column(type="integer")
     * @OneToMany(targetEntity="Order", mappedBy="status") 
    **/
    protected $id;

    /** 
     * @Column(type="string") 
    **/
    protected $value;

    public function getId()
    {
        return $this->id;
    }
    public function getValue()
    {
        return $this->value;
    }
    public function setValue($value)
    {
        $this->value = $value;
    }
}