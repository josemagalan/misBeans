<?php

namespace BaseBundle\Tests\Logic;


use BaseBundle\Controller\Logic\OfertaLogic;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class OfertaLogicTest extends KernelTestCase
{
    public function testConsts()
    {
        $this->assertEquals(0, OfertaLogic::NOTRATADA);
        $this->assertEquals(1, OfertaLogic::ACEPTADA);
        $this->assertEquals(2, OfertaLogic::RECHAZADA);
        $this->assertEquals(3, OfertaLogic::CONCLUIDA);
    }
}