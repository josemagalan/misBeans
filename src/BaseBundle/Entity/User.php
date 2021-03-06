<?php

namespace BaseBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="BaseBundle\Entity\UserRepository")
 */
class User extends BaseUser
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="full_name", type="string", length=255, nullable=false)
     */
    protected $fullName;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="BaseBundle\Entity\Partida", mappedBy="idUser")
     */
    protected $idPartida;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->idPartida = new ArrayCollection();
        parent::__construct();
    }


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set fullName
     *
     * @param string $fullName
     *
     * @return User
     */
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;

        return $this;
    }

    /**
     * Get fullName
     *
     * @return string
     */
    public function getFullName()
    {
        return $this->fullName;
    }



    /**
     * Add idPartida
     *
     * @param \BaseBundle\Entity\Partida $idPartida
     *
     * @return User
     */
    public function addIdPartida(\BaseBundle\Entity\Partida $idPartida)
    {
        $this->idPartida[] = $idPartida;

        return $this;
    }

    /**
     * Remove idPartida
     *
     * @param \BaseBundle\Entity\Partida $idPartida
     */
    public function removeIdPartida(\BaseBundle\Entity\Partida $idPartida)
    {
        $this->idPartida->removeElement($idPartida);
    }

    /**
     * Get idPartida
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIdPartida()
    {
        return $this->idPartida;
    }

    /**
     * @return \DateTime
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    /**
     * @return \DateTime
     */
    public function getCredentialsExpireAt()
    {
        return $this->credentialsExpireAt;
    }
}
