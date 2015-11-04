<?php

namespace BaseBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AcceptRejectDealType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('idC', 'hidden',array('required' => true));
        $builder->add('idO', 'hidden',array('required' => true));
        $builder->add('idP', 'hidden',array('required' => true));
        $builder->add('idD', 'hidden',array('required' => true));
        $builder->add('accept', 'submit' );
        $builder->add('reject', 'submit' );
    }

    public function getName()
    {
        return 'acceptOrReject_form';
    }
}