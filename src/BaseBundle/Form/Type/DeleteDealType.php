<?php

namespace BaseBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class DeleteDealType extends AbstractType
{
    /*  idC -> idCreador
        idD -> idDestinatario
        idP -> idPartida
        idO -> idOferta
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('idC', 'hidden', array('required' => true));
        $builder->add('idO', 'hidden', array('required' => true));
        $builder->add('idD', 'hidden', array('required' => true));
        $builder->add('idP', 'hidden', array('required' => true));
        $builder->add('del', 'submit');
    }

    public function getName()
    {
        return 'delDeal_form';
    }

}