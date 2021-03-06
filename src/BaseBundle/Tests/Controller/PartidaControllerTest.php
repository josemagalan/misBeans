<?php

namespace BaseBundle\Tests\Controller;


use BaseBundle\Tests\Auth;

class PartidaControllerTest extends Auth
{
    public function testPartidaRegisterAction()
    {
        $this->client->request('GET', '/es/userhome/register/1', array('id_partida' => 1));

        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->assertEquals('/es/userhome/1', $this->client->getResponse()->headers->get('location'));
    }

    public function testPartidaAction()
    {
        $crawler = $this->client->request('GET', '/es/userhome/1', array('id_partida' => 1));
        $this->assertTrue($crawler->filter('html:contains("Prueba1")')->count() > 0);
    }

    public function testAcceptRejectAction()
    {
        $crawler = $this->client->request('GET', '/es/userhome/accept_reject_deal');
        $this->assertTrue($crawler->filter('html:contains("405 Method Not Allowed")')->count() > 0);
    }

    public function testDeleteDealAction()
    {
        $crawler = $this->client->request('GET', '/es/userhome/delete_deal');
        $this->assertTrue($crawler->filter('html:contains("405 Method Not Allowed")')->count() > 0);
    }

    public function testJugadorAction()
    {
        $crawler = $this->client->request('GET', '/es/userhome/1/userTest',
            array('id_partida' => 1, 'username' => 'userTest'));

        $this->assertTrue($crawler->filter('html:contains("Proposición de cambio")')->count() > 0);
    }

    public function testCreateDealAction()
    {
        $crawler = $this->client->request('GET', '/es/userhome/create_deal');
        $this->assertTrue($crawler->filter('html:contains("405 Method Not Allowed")')->count() > 0);
    }
}