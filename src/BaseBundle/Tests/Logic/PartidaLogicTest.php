<?php

namespace BaseBundle\Tests\Logic;


use BaseBundle\Controller\Logic\PartidaLogic;
use BaseBundle\Entity\Partida;
use BaseBundle\Entity\UserPartida;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PartidaLogicTest extends KernelTestCase
{
    private $partidaLogic;
    private $em;

    protected function setUp()
    {
        static::bootKernel();
        $this->partidaLogic = new PartidaLogic();
        $this->em = static::$kernel->getContainer()->get('doctrine')->getManager();
    }

    public function testConsts()
    {
        $this->assertEquals(0, PartidaLogic::ENCURSO);
        $this->assertEquals(1, PartidaLogic::TERMINADO);
    }

    public function testCalculateBeans()
    {
        $partidaData = $this->em->getRepository('BaseBundle:Partida')->findOneById(1);
        $nJugadores = 10;
        $resultadoEsperado = array('aluRoja' => 120, 'aluBlanca' => 180, 'total' => 300);
        $resultado = $this->partidaLogic->calculateBeans($partidaData, $nJugadores);
        $this->assertEquals($resultado, $resultadoEsperado);

    }

    public function testCalculateFUtilidad()
    {
        /** @var Partida $partida */
        $partida = $this->em->getRepository('BaseBundle:Partida')->findOneById(1);
        $resultado = $this->partidaLogic->calculateFUtilidad(20, 20, $partida);
        $this->assertEquals($resultado, 8000);
    }

    public function testCheckInProgress()
    {
        $now = new DateTime('now');
        $ofertas = array(array('tiempoOferta' => 10, 'creado' => $now));
        $resultado = $this->partidaLogic->checkInProgress($ofertas, $this->em);
        $this->assertEquals($resultado, $ofertas);
    }

    public function testPartidaGraphic()
    {
        /** @var Partida $partida */
        $partida = $this->em->getRepository('BaseBundle:Partida')->findOneById(1);
        /** @var array $evo */
        $evo = $this->partidaLogic->partidaEvolution($this->em, 8, $partida);
        /** @var UserPartida $userpartida */
        $userpartida = $this->em->getRepository('BaseBundle:UserPartida')->findByIDS(8, $partida->getId());
        $resultado = array_pop($evo);
        $this->assertEquals($resultado['aluBlanca'], $userpartida->getAluBlancaActual());
        $this->assertEquals($resultado['aluRoja'], $userpartida->getAluRojaActual());
        $this->assertEquals($resultado['fUtilidad'], $userpartida->getFUtilidad());

    }
}