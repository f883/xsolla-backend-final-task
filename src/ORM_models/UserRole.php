<?php
// ItemType.php
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity @Table(name="user_roles")
 */
class UserRole
{
    public static $USER = 'user';
    public static $ADMIN = 'admin';

    public function __construct(){
        $this->$role = UserRole::$USER;
    }

    /** 
     * @Id @GeneratedValue @Column(type="integer")
     * @OneToMany(targetEntity="User", mappedBy="role") 
    **/
    protected $id;

    /** 
     * @Column(type="string") 
    **/
    protected $role;

    public function getId()
    {
        return $this->id;
    }
    public function getRole()
    {
        return $this->role;
    }
    public function setRole($role)
    {
        $this->role = $role;
    }
}