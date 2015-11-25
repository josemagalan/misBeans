<?php

namespace BaseBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * UserPartida
 *
 * @ORM\Table(name="userpartida", indexes={@ORM\Index(name="id_partida", columns={"id_partida"})} )
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="BaseBundle\Entity\UserPartidaRepository")
 */
class UserPartida
{

    /**
     * @var \BaseBundle\Entity\Partida
     * @ORM\ManyToMany(targetEntity="BaseBundle\Entity\Partida")
     * @ORM\JoinTable(name="userpartida",
     *   joinColumns={
     *     @ORM\JoinColumn(name="id_partida", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="id_user", referencedColumnName="id")
     *   }
     * )
     * @ORM\Id
     * @ORM\Column(name="id_partida")
     */
    private $idPartida;

    /**
     * @var \BaseBundle\Entity\User
     * @ORM\ManyToMany(targetEntity="BaseBundle\Entity\User")
     * @ORM\JoinTable(name="userpartida",
     *   joinColumns={
     *     @ORM\JoinColumn(name="id_user", referencedColumnName="id")
     *   }
     * ),
     *   inverseJoinColumns={
     * @ORM\JoinColumn(name="id_partida", referencedColumnName="id")
     *   }
     *
     * @ORM\Id
     * @ORM\Column(name="id_user")
     */
    private $idUser;

    /**
     * @var integer
     *
     * @ORM\Column(name="alu_roja_inicial", type="integer", nullable=false)
     */
    private $aluRojaInicial = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="alu_blanca_inicial", type="integer", nullable=false)
     */
    private $aluBlancaInicial = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="alu_roja_actual", type="integer", nullable=false)
     */
    private $aluRojaActual = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="alu_blanca_actual", type="integer", nullable=false)
     */
    private $aluBlancaActual = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="f_utilidad", type="integer", nullable=false)
     */
    private $fUtilidad = '0';

    /**
     * UserPartida constructor.
     * @param Partida $idPartida
     * @param User $idUser
     * @param int $aluRojaInicial
     * @param int $aluBlancaInicial
     */
    public function __construct(Partida $idPartida, User $idUser, $aluRojaInicial, $aluBlancaInicial, $aluRojaActual, $aluBlancaActual)
    {
        $this->idPartida = $idPartida;
        $this->idUser = $idUser;
        $this->aluRojaInicial = $aluRojaInicial;
        $this->aluBlancaInicial = $aluBlancaInicial;
        $this->aluRojaActual = $aluRojaActual;
        $this->aluBlancaActual = $aluBlancaActual;
    }

    /**
     * @return Partida
     */
    public function getIdPartida()
    {
        return $this->idPartida;
    }

    /**
     * @param Partida $idPartida
     */
    public function setIdPartida($idPartida)
    {
        $this->idPartida = $idPartida;
    }

    /**
     * @return User
     */
    public function getIdUser()
    {
        return $this->idUser;
    }

    /**
     * @param User $idUser
     */
    public function setIdUser($idUser)
    {
        $this->idUser = $idUser;
    }

    /**
     * @return int
     */
    public function getAluRojaInicial()
    {
        return $this->aluRojaInicial;
    }

    /**
     * @param int $aluRojaInicial
     */
    public function setAluRojaInicial($aluRojaInicial)
    {
        $this->aluRojaInicial = $aluRojaInicial;
    }

    /**
     * @return int
     */
    public function getAluBlancaInicial()
    {
        return $this->aluBlancaInicial;
    }

    /**
     * @param int $aluBlancaInicial
     */
    public function setAluBlancaInicial($aluBlancaInicial)
    {
        $this->aluBlancaInicial = $aluBlancaInicial;
    }

    /**
     * @return int
     */
    public function getAluRojaActual()
    {
        return $this->aluRojaActual;
    }

    /**
     * @param int $aluRojaActual
     */
    public function setAluRojaActual($aluRojaActual)
    {
        $this->aluRojaActual = $aluRojaActual;
    }

    /**
     * @return int
     */
    public function getAluBlancaActual()
    {
        return $this->aluBlancaActual;
    }

    /**
     * @param int $aluBlancaActual
     */
    public function setAluBlancaActual($aluBlancaActual)
    {
        $this->aluBlancaActual = $aluBlancaActual;
    }

    /**
     * @return int
     */
    public function getFUtilidad()
    {
        return $this->fUtilidad;
    }

    /**
     * @param int $fUtilidad
     */
    public function setFUtilidad($fUtilidad)
    {
        $this->fUtilidad = $fUtilidad;
    }


}