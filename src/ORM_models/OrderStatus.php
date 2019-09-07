<?php
// ItemType.php
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity @Table(name="order_statuses")
 */
class OrderStatus
{
    public static $ACTIVE = 'ACTIVE';
    public static $SOLD = 'SOLD';
    public static $CANCELLED = 'CANCELLED';

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