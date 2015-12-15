<?php

namespace BaseBundle\Tests\Forms;


use BaseBundle\Form\Type\AcceptRejectDealType;

class AcceptRejectDealTypeTest extends Type
{
    public function testType()
    {
        $type = new AcceptRejectDealType();
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