<?php

namespace BaseBundle\Tests\Logic;


use BaseBundle\Controller\Logic\UserPartidaLogic;
use BaseBundle\Entity\Ofertas;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserPartidaLogicTest extends KernelTestCase
{
    private $userPartidaLogic;
    private $em;

    protected function setUp()
    {
        static::bootKernel();
        $this->userPartidaLogic = new UserPartidaLogic();
        $this->em = static::$kernel->getContainer()->get('doctrine')->getManager();
    }

    public function testbeansStatus()
    {
        /** @var Ofertas $partida */
        $oferta = $this->em->getRepository('BaseBundle:Ofertas')->findOneById(1);

        $resultado = $this->userPartidaLogic->beansStatus($oferta, 1);
        $resultadoEsperado = array('aluRoja' => 2, 'aluBlanca' => -4);
        $this->assertEquals($resultado, $resultadoEsperado);

        $resultado = $this->userPartidaLogic->beansStatus($oferta, 2);
        $resultadoEsperado = array('aluRoja' => -2, 'aluBlanca' => 4);
        $this->assertEquals($resultado, $resultadoEsperado);

    }
}