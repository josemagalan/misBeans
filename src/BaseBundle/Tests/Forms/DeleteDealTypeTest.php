<?php

namespace BaseBundle\Tests\Forms;


use BaseBundle\Form\Type\DeleteDealType;

class DeleteDealTypeTest extends Type
{
    public function testType()
    {
        $type = new DeleteDealType();
        $form = $this->formFactory->create($type);
        $formData = array(
            'idC' => 2,
            'idO' => 8,
            'idD' => 8,
            'idP' => 1
        );

        $form->submit($formData);
        $data = $form->getData();

        $this->assertTrue($form->isSynchronized());
        $this->assertFalse($form->isValid());
        $this->assertEquals($data['idC'], 2);
    }
}