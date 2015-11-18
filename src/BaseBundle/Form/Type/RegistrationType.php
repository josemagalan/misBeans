<?php

namespace BaseBundle\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('fullname', 'text', array('required' => true, 'label' => 'user.fullName', 'translation_domain' => 'BaseBundle'));
    }

    public function getParent()
    {
        return 'fos_user_registration';
    }


    public function getName()
    {
        return 'baseBundle_registration';
    }
}