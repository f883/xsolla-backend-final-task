<?php
// ItemType.php
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity @Table(name="item_types")
 */
class ItemType
{
    /** 
     * @Id @GeneratedValue @Column(type="integer")
     * @ManyToOne(targetEntity="Item", inversedBy="itemType")
    **/
    protected $id;

    /** 
     * @Column(type="string") 
    **/
    protected $name;

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
}