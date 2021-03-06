<?php

namespace BaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Partida
 *
 * @ORM\Table(name="partida", indexes={@ORM\Index(name="id_creador", columns={"id_creador"})})
 * @ORM\Entity(repositoryClass="BaseBundle\Entity\PartidaRepository")
 */
class Partida
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255, nullable=false)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255, nullable=true)
     */
    private $password;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="creado", type="datetime", nullable=false)
     */
    private $creado;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fin", type="datetime", nullable=false)
     */
    private $fin;

    /**
     * @var integer
     *
     * @ORM\Column(name="max_jugadores", type="integer", nullable=false)
     */
    private $maxJugadores;

    /**
     * @var integer
     *
     * @ORM\Column(name="max_ofertas", type="integer", nullable=false)
     */
    private $maxOfertas;

    /**
     * @var integer
     *
     * @ORM\Column(name="tiempo_oferta", type="integer", nullable=false)
     */
    private $tiempoOferta;

    /**
     * @var float
     *
     * @ORM\Column(name="ratio", type="float", precision=3, scale=2, nullable=false)
     */
    private $ratio;

    /**
     * @var integer
     *
     * @ORM\Column(name="alu_por_usuario", type="integer", nullable=false)
     */
    private $aluPorUsuario;

    /**
     * @var integer
     *
     * @ORM\Column(name="exp_y", type="integer", nullable=false)
     */
    private $expY;

    /**
     * @var integer
     *
     * @ORM\Column(name="exp_z", type="integer", nullable=false)
     */
    private $expZ;

    /**
     * @var boolean
     *
     * @ORM\Column(name="empezado", type="boolean", nullable=false)
     */
    private $empezado = '0';

    /**
     * @var \BaseBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="BaseBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_creador", referencedColumnName="id")
     * })
     */
    private $idCreador;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="BaseBundle\Entity\User", inversedBy="idPartida")
     * @ORM\JoinTable(name="userpartida",
     *   joinColumns={
     *     @ORM\JoinColumn(name="id_partida", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="id_user", referencedColumnName="id")
     *   }
     * )
     */
    private $idUser;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->idUser = new \Doctrine\Common\Collections\ArrayCollection();
    }


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     *
     * @return Partida
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return Partida
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set creado
     *
     * @param \DateTime $creado
     *
     * @return Partida
     */
    public function setCreado($creado)
    {
        $this->creado = $creado;

        return $this;
    }

    /**
     * Get creado
     *
     * @return \DateTime
     */
    public function getCreado()
    {
        return $this->creado;
    }

    /**
     * Set fin
     *
     * @param \DateTime $fin
     *
     * @return Partida
     */
    public function setFin($fin)
    {
        $this->fin = $fin;

        return $this;
    }

    /**
     * Get fin
     *
     * @return \DateTime
     */
    public function getFin()
    {
        return $this->fin;
    }

    /**
     * Set maxJugadores
     *
     * @param integer $maxJugadores
     *
     * @return Partida
     */
    public function setMaxJugadores($maxJugadores)
    {
        $this->maxJugadores = $maxJugadores;

        return $this;
    }

    /**
     * Get maxJugadores
     *
     * @return integer
     */
    public function getMaxJugadores()
    {
        return $this->maxJugadores;
    }

    /**
     * Set maxOfertas
     *
     * @param integer $maxOfertas
     *
     * @return Partida
     */
    public function setMaxOfertas($maxOfertas)
    {
        $this->maxOfertas = $maxOfertas;

        return $this;
    }

    /**
     * Get maxOfertas
     *
     * @return integer
     */
    public function getMaxOfertas()
    {
        return $this->maxOfertas;
    }

    /**
     * Set tiempoOferta
     *
     * @param integer $tiempoOferta
     *
     * @return Partida
     */
    public function setTiempoOferta($tiempoOferta)
    {
        $this->tiempoOferta = $tiempoOferta;

        return $this;
    }

    /**
     * Get tiempoOferta
     *
     * @return integer
     */
    public function getTiempoOferta()
    {
        return $this->tiempoOferta;
    }

    /**
     * Set ratio
     *
     * @param float $ratio
     *
     * @return Partida
     */
    public function setRatio($ratio)
    {
        $this->ratio = $ratio;

        return $this;
    }

    /**
     * Get ratio
     *
     * @return float
     */
    public function getRatio()
    {
        return $this->ratio;
    }

    /**
     * Set aluPorUsuario
     *
     * @param integer $aluPorUsuario
     *
     * @return Partida
     */
    public function setAluPorUsuario($aluPorUsuario)
    {
        $this->aluPorUsuario = $aluPorUsuario;

        return $this;
    }

    /**
     * Get aluPorUsuario
     *
     * @return integer
     */
    public function getAluPorUsuario()
    {
        return $this->aluPorUsuario;
    }

    /**
     * Set expY
     *
     * @param integer $expY
     *
     * @return Partida
     */
    public function setExpY($expY)
    {
        $this->expY = $expY;

        return $this;
    }

    /**
     * Get expY
     *
     * @return integer
     */
    public function getExpY()
    {
        return $this->expY;
    }

    /**
     * Set expZ
     *
     * @param integer $expZ
     *
     * @return Partida
     */
    public function setExpZ($expZ)
    {
        $this->expZ = $expZ;

        return $this;
    }

    /**
     * Get expZ
     *
     * @return integer
     */
    public function getExpZ()
    {
        return $this->expZ;
    }

    /**
     * Set empezado
     *
     * @param boolean $empezado
     *
     * @return Partida
     */
    public function setEmpezado($empezado)
    {
        $this->empezado = $empezado;

        return $this;
    }

    /**
     * Get empezado
     *
     * @return boolean
     */
    public function getEmpezado()
    {
        return $this->empezado;
    }

    /**
     * Set idCreador
     *
     * @param \BaseBundle\Entity\User $idCreador
     *
     * @return Partida
     */
    public function setIdCreador(\BaseBundle\Entity\User $idCreador = null)
    {
        $this->idCreador = $idCreador;

        return $this;
    }

    /**
     * Get idCreador
     *
     * @return \BaseBundle\Entity\User
     */
    public function getIdCreador()
    {
        return $this->idCreador;
    }

    /**
     * Add idUser
     *
     * @param \BaseBundle\Entity\User $idUser
     *
     * @return Partida
     */
    public function addIdUser(\BaseBundle\Entity\User $idUser)
    {
        $this->idUser[] = $idUser;

        return $this;
    }

    /**
     * Remove idUser
     *
     * @param \BaseBundle\Entity\User $idUser
     */
    public function removeIdUser(\BaseBundle\Entity\User $idUser)
    {
        $this->idUser->removeElement($idUser);
    }

    /**
     * Get idUser
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIdUser()
    {
        return $this->idUser;
    }

    public function __toString()
    {
        return (string) $this->getId();
    }
}
