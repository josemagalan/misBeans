<?php

namespace BaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ofertas
 *
 * @ORM\Table(name="ofertas", indexes={@ORM\Index(name="idCreador", columns={"idCreador"}), @ORM\Index(name="idDestinatario", columns={"idDestinatario"}), @ORM\Index(name="idPartida", columns={"idPartida"})})
 * @ORM\Entity
 */
class Ofertas
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
     * @var \DateTime
     *
     * @ORM\Column(name="creado", type="datetime", nullable=false)
     */
    private $creado;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modificado", type="datetime", nullable=true)
     */
    private $modificado;

    /**
     * @var boolean
     *
     * @ORM\Column(name="estado", type="boolean", nullable=false)
     */
    private $estado;

    /**
     * @var integer
     *
     * @ORM\Column(name="aluBlancaIn", type="integer", nullable=false)
     */
    private $aluBlancaIn;

    /**
     * @var integer
     *
     * @ORM\Column(name="aluRojaIn", type="integer", nullable=false)
     */
    private $aluRojaIn;

    /**
     * @var integer
     *
     * @ORM\Column(name="aluBlancaOut", type="integer", nullable=false)
     */
    private $aluBlancaOut;

    /**
     * @var integer
     *
     * @ORM\Column(name="aluRojaOut", type="integer", nullable=false)
     */
    private $aluRojaOut;

    /**
     * @var \BaseBundle\Entity\FosUser
     *
     * @ORM\ManyToOne(targetEntity="BaseBundle\Entity\FosUser")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idCreador", referencedColumnName="id")
     * })
     */
    private $idCreador;

    /**
     * @var \BaseBundle\Entity\FosUser
     *
     * @ORM\ManyToOne(targetEntity="BaseBundle\Entity\FosUser")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idDestinatario", referencedColumnName="id")
     * })
     */
    private $idDestinatario;

    /**
     * @var \BaseBundle\Entity\Partida
     *
     * @ORM\ManyToOne(targetEntity="BaseBundle\Entity\Partida")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idPartida", referencedColumnName="id")
     * })
     */
    private $idPartida;



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
     * Set creado
     *
     * @param \DateTime $creado
     *
     * @return Ofertas
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
     * Set modificado
     *
     * @param \DateTime $modificado
     *
     * @return Ofertas
     */
    public function setModificado($modificado)
    {
        $this->modificado = $modificado;

        return $this;
    }

    /**
     * Get modificado
     *
     * @return \DateTime
     */
    public function getModificado()
    {
        return $this->modificado;
    }

    /**
     * Set estado
     *
     * @param boolean $estado
     *
     * @return Ofertas
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;

        return $this;
    }

    /**
     * Get estado
     *
     * @return boolean
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * Set aluBlancaIn
     *
     * @param integer $aluBlancaIn
     *
     * @return Ofertas
     */
    public function setAluBlancaIn($aluBlancaIn)
    {
        $this->aluBlancaIn = $aluBlancaIn;

        return $this;
    }

    /**
     * Get aluBlancaIn
     *
     * @return integer
     */
    public function getAluBlancaIn()
    {
        return $this->aluBlancaIn;
    }

    /**
     * Set aluRojaIn
     *
     * @param integer $aluRojaIn
     *
     * @return Ofertas
     */
    public function setAluRojaIn($aluRojaIn)
    {
        $this->aluRojaIn = $aluRojaIn;

        return $this;
    }

    /**
     * Get aluRojaIn
     *
     * @return integer
     */
    public function getAlurojain()
    {
        return $this->aluRojaIn;
    }

    /**
     * Set alublancaout
     *
     * @param integer $alublancaout
     *
     * @return Ofertas
     */
    public function setAluBlancaOut($aluBlancaOut)
    {
        $this->aluBlancaOut = $aluBlancaOut;

        return $this;
    }

    /**
     * Get alublancaout
     *
     * @return integer
     */
    public function getAluBlancaOut()
    {
        return $this->aluBlancaOut;
    }

    /**
     * Set aluRojaOut
     *
     * @param integer $aluRojaOut
     *
     * @return Ofertas
     */
    public function setAluRojaOut($aluRojaOut)
    {
        $this->aluRojaOut = $aluRojaOut;

        return $this;
    }

    /**
     * Get aluRojaOut
     *
     * @return integer
     */
    public function getAluRojaOut()
    {
        return $this->aluRojaOut;
    }

    /**
     * Set idCreador
     *
     * @param \BaseBundle\Entity\FosUser $idCreador
     *
     * @return Ofertas
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
     * Set idDestinatario
     *
     * @param \BaseBundle\Entity\FosUser $idDestinatario
     *
     * @return Ofertas
     */
    public function setIdDestinatario(\BaseBundle\Entity\FosUser $idDestinatario = null)
    {
        $this->idDestinatario = $idDestinatario;

        return $this;
    }

    /**
     * Get idDestinatario
     *
     * @return \BaseBundle\Entity\FosUser
     */
    public function getIdDestinatario()
    {
        return $this->idDestinatario;
    }

    /**
     * Set idPartida
     *
     * @param \BaseBundle\Entity\Partida $idPartida
     *
     * @return Ofertas
     */
    public function setIdPartida(\BaseBundle\Entity\Partida $idPartida = null)
    {
        $this->idPartida = $idPartida;

        return $this;
    }

    /**
     * Get idPartida
     *
     * @return \BaseBundle\Entity\Partida
     */
    public function getIdPartida()
    {
        return $this->idPartida;
    }
}
