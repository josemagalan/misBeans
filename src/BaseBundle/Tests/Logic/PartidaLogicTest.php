<?php

namespace BaseBundle\Tests\Logic;


use BaseBundle\Controller\Logic\PartidaLogic;
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
        $resultadoEsperado = array('aluRoja' => 120, 'aluBlanca' => 180, 'totales' => 300);
        $resultado = $this->partidaLogic->calculateBeans($partidaData, $nJugadores);
        $this->assertEquals($resultado, $resultadoEsperado);
    }

    public function testCheckInProgress()
    {
        $now = new DateTime('now');
        $ofertas = array(array('tiempoOferta'=> 10, 'creado'=> $now));
        $resultado = $this->partidaLogic->checkInProgress($ofertas, $this->em);
        $this->assertEquals($resultado, $ofertas);
    }
}