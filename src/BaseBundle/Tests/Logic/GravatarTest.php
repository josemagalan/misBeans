<?php

namespace BaseBundle\Tests\Logic;


use BaseBundle\Controller\Logic\Gravatar;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class GravatarTest extends KernelTestCase
{
    protected function setUp()
    {
        static::bootKernel();
    }

    public function testGravatar()
    {
        $email = 'smm0063@alu.ubu.es';
        $gravatar = new Gravatar($email);
        $avatar = $gravatar->get_gravatar();
        $cadenaEsperada = 'http://www.gravatar.com/avatar/8340bce7d7920b54bb7e1529c8386729?s=60&d=mm&r=g';

        $this->assertEquals($avatar, $cadenaEsperada);
    }
}