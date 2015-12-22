<?php

namespace BaseBundle\Tests\Logic;


use BaseBundle\Controller\Logic\GraphicsLogic;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class GraphicsLogicTest extends KernelTestCase
{
    private $graphicsLogic;

    protected function setUp()
    {
        static::bootKernel();
        $this->graphicsLogic = new GraphicsLogic();
    }

    public function testRandomColor(){
        $resultado = $this->graphicsLogic->randomColor();
        $patron = '/^#{1}\S{5}/';
        $this->assertEquals(true, preg_match($patron,$resultado));
    }
}