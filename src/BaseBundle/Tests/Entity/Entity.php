<?php

namespace BaseBundle\Tests\Entity;

use BaseBundle\Entity\Log;
use BaseBundle\Entity\Ofertas;
use BaseBundle\Entity\Partida;
use BaseBundle\Entity\User;
use DateInterval;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class Entity extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $emMod;
    /**
     * @var \Doctrine\ORM\EntityRepository
     */
    protected $repository;
    /**
     * @var \DateTime
     */
    protected $now;
    /**
     * @var Partida
     */
    protected $partida;
    /**
     * @var User
     */
    protected $user;
    /**
     * @var Ofertas
     */
    protected $oferta;
    /**
     * @var Log
     */
    protected $log;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        self::bootKernel();
        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    /**
     * Initializes variables to mock an Entity
     */
    protected function mock()
    {
        $this->emMod = $this
            ->getMockBuilder('\Doctrine\Common\Persistence\ObjectManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->repository = $this
            ->getMockBuilder('\Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock();


        $this->now = new \DateTime('now');
        $this->user = $this->em->getRepository('BaseBundle:User')->find(2);

        /*mokear una partida*/
        $this->partida = new Partida();

        $this->partida->setAluPorUsuario(20)
            ->setRatio(0.5)
            ->setCreado($this->now)
            ->setFin($this->now->add(new DateInterval('P10D')))
            ->setMaxJugadores(10)
            ->setNombre('test1?A')
            ->setPassword('test')
            ->setExpY(1)
            ->setExpZ(1);
        $this->partida
            ->setIdCreador($this->user)
            ->setMaxOfertas(2)
            ->setTiempoOferta(10);

        /*mokear una oferta*/
        $this->oferta = new Ofertas();
        $this->oferta->setCreado($this->now)
            ->setEstado(0)
            ->setAluBlancaIn(10)
            ->setAluBlancaOut(0)
            ->setAluRojaIn(0)
            ->setAluRojaOut(6)
            ->setIdCreador($this->user)
            ->setIdDestinatario($this->user)
            ->setIdPartida($this->partida);

        /*mokear un log*/
        $this->log = new Log();
        $this->log->setUser($this->user)
            ->setActionId(5)
            ->setFecha($this->now);
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();

        //cerrar emMod
        /*$this->emMod = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        $this->emMod->close();*/
    }
}