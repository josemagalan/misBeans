<?php

namespace BaseBundle\Entity;


use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 * @ORM\Entity(repositoryClass="BaseBundle\Entity\UserRepository")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * User full name
     *
     * @ORM\Column(type="string", length=255)
     *
     */
    protected $fullName;


    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * @param string $fullName
     * @return string
     */
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;

        return $this;
    }


}