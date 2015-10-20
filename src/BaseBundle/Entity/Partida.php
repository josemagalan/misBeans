<?php

namespace BaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Partida
 *
 * @ORM\Table(name="partida", indexes={@ORM\Index(name="id_creador", columns={"id_creador"})})
 * @ORM\Entity
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
     * @var \DateTime
     *
     * @ORM\Column(name="creado", type="datetime", nullable=false)
     */
    private $creado;

    /**
     * @var integer
     *
     * @ORM\Column(name="tiempo", type="integer", nullable=false)
     */
    private $tiempo;

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
     * @var boolean
     *
     * @ORM\Column(name="alg_utilidad", type="boolean", nullable=false)
     */
    private $algUtilidad;

    /**
     * @var boolean
     *
     * @ORM\Column(name="alg_reparto", type="boolean", nullable=false)
     */
    private $algReparto;

    /**
     * @var integer
     *
     * @ORM\Column(name="alu_roja", type="integer", nullable=false)
     */
    private $aluRoja;

    /**
     * @var integer
     *
     * @ORM\Column(name="alu_blanca", type="integer", nullable=false)
     */
    private $aluBlanca;

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
     * @ORM\JoinTable(name="jugadores",
     *   joinColumns={
     *     @ORM\JoinColumn(name="id_partida", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="id_jugador", referencedColumnName="id")
     *   }
     * )
     */
    private $idJugador;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->idJugador = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set tiempo
     *
     * @param integer $tiempo
     *
     * @return Partida
     */
    public function setTiempo($tiempo)
    {
        $this->tiempo = $tiempo;

        return $this;
    }

    /**
     * Get tiempo
     *
     * @return integer
     */
    public function getTiempo()
    {
        return $this->tiempo;
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
     * Set algUtilidad
     *
     * @param boolean $algUtilidad
     *
     * @return Partida
     */
    public function setAlgUtilidad($algUtilidad)
    {
        $this->algUtilidad = $algUtilidad;

        return $this;
    }

    /**
     * Get algUtilidad
     *
     * @return boolean
     */
    public function getAlgUtilidad()
    {
        return $this->algUtilidad;
    }

    /**
     * Set algReparto
     *
     * @param boolean $algReparto
     *
     * @return Partida
     */
    public function setAlgReparto($algReparto)
    {
        $this->algReparto = $algReparto;

        return $this;
    }

    /**
     * Get algReparto
     *
     * @return boolean
     */
    public function getAlgReparto()
    {
        return $this->algReparto;
    }

    /**
     * Set aluRoja
     *
     * @param integer $aluRoja
     *
     * @return Partida
     */
    public function setAluRoja($aluRoja)
    {
        $this->aluRoja = $aluRoja;

        return $this;
    }

    /**
     * Get aluRoja
     *
     * @return integer
     */
    public function getAluRoja()
    {
        return $this->aluRoja;
    }

    /**
     * Set aluBlanca
     *
     * @param integer $aluBlanca
     *
     * @return Partida
     */
    public function setAluBlanca($aluBlanca)
    {
        $this->aluBlanca = $aluBlanca;

        return $this;
    }

    /**
     * Get aluBlanca
     *
     * @return integer
     */
    public function getAluBlanca()
    {
        return $this->aluBlanca;
    }

    /**
     * Set idCreador
     *
     * @param \BaseBundle\Entity\FosUser $idCreador
     *
     * @return Partida
     */
    public function setIdCreador(\BaseBundle\Entity\FosUser $idCreador = null)
    {
        $this->idCreador = $idCreador;

        return $this;
    }

    /**
     * Get idCreador
     *
     * @return \BaseBundle\Entity\FosUser
     */
    public function getIdCreador()
    {
        return $this->idCreador;
    }

    /**
     * Add idJugador
     *
     * @param \BaseBundle\Entity\FosUser $idJugador
     *
     * @return Partida
     */
    public function addIdJugador(\BaseBundle\Entity\FosUser $idJugador)
    {
        $this->idJugador[] = $idJugador;

        return $this;
    }

    /**
     * Remove idJugador
     *
     * @param \BaseBundle\Entity\FosUser $idJugador
     */
    public function removeIdJugador(\BaseBundle\Entity\FosUser $idJugador)
    {
        $this->idJugador->removeElement($idJugador);
    }

    /**
     * Get idJugador
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIdJugador()
    {
        return $this->idJugador;
    }
}
