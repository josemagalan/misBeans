<?php

namespace BaseBundle\Tests\Controller;

use BaseBundle\Tests\Auth;

class AdminControllerTest extends Auth
{

    public function testAdminHomeNoAdmin()
    {
        $this->client->request('GET', '/es/adminhome/');

        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->assertEquals('/', $this->client->getResponse()->headers->get('location'));
    }

    public function testAdminHomeAdmin(){
        $admin = $this->createAuthorizedAdmin();
        $admin->request('GET', '/es/adminhome/');
        $this->assertTrue($admin->getCrawler()->filter('html:contains("Nueva partida")')->count() > 0);
    }

    public function testNewGameAction(){
        $admin = $this->createAuthorizedAdmin();
        $admin->request('GET', '/es/adminhome/new');
        $this->assertTrue($admin->getCrawler()->filter('html:contains("Exponentes de Cobb Douglas")')->count() > 0);
    }

    public function testStatisticsAction(){
        $admin = $this->createAuthorizedAdmin();
        $admin->request('GET', '/es/adminhome/stats/1', array('id_partida' => 1));
        $this->assertTrue($admin->getCrawler()->filter('html:contains("Descargar")')->count() > 0);
    }

    /*public function testRankingDownloadAction(){
        $admin = $this->createAuthorizedAdmin();
        $admin->setServerParameter('ACCEPT', 'text/csv');
        $admin->request(
            'GET',
            '/es/adminhome/stats/1/download/',
            array(),
            array(),
            array(
                'accept' => 'text/csv',
            )
        );
    }*/
}

