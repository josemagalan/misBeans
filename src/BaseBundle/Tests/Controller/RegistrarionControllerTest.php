<?php

namespace BaseBundle\Tests\Controller;


use BaseBundle\Tests\Auth;

class RegistrationControllerTest extends Auth
{
    public function testRegisterAction()
    {
        $crawler = $this->client->request('GET', '/es/register');
        $this->assertTrue($crawler->filter('html:contains("Repita la contraseña")')->count() > 0);
    }
}