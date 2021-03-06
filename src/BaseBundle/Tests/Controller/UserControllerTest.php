<?php

namespace BaseBundle\Tests\Controller;

use BaseBundle\Form\Type\RegistrationType;

use BaseBundle\Tests\Auth;

class UserControllerTest extends Auth
{

    public function testUserhomeAction (){
        $crawler = $this->client->request('GET', '/es/userhome/');
        $this->assertTrue($crawler->filter('html:contains("Buscar partida")')->count() > 0);
    }

    public function testProfileAction (){
        $crawler = $this->client->request('GET', '/es/userhome/profile');
        $this->assertTrue($crawler->filter('html:contains("Actividad")')->count() > 0);

        $formData = array(
            'fullName'  => 'Sergio Martín Marquina',
            'email'  => 'smm0063@alu.ubu.es',
        );

        $type = new RegistrationType();
        $form = $this->formFactory->create($type);

        // submit the data to the form directly
        $form->submit($formData);

        //search user
        $user = $this->em->getRepository('BaseBundle:User')->find(8);
        $this->assertEquals($user->getEmail(), $form->getData()->getEmail());
    }
}