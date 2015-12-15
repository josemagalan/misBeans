<?php
namespace BaseBundle\Tests\Forms;


use BaseBundle\Form\Type\NewGameType;

class NewGameTypeTest extends Type
{
    public function testType()
    {
        $type = new NewGameType;
        $form = $this->formFactory->create($type);
        $formData = array(
            'nombre' => 'test1A',
            'password' => 'password',
            'maxJugadores' => 100,
            'maxOfertas' => 10,
            'tiempoOferta' => 10,
            'aluPUsuario' => 50,
            'ratio' => 0,
            'expY' => 1,
            'expZ' => 1
        );

        $form->submit($formData);
        $data = $form->getData();

        $this->assertTrue($form->isSynchronized());
        $this->assertFalse($form->isValid());
        $this->assertEquals($data['nombre'], 'test1A');
    }
}