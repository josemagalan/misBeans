<?php

namespace BaseBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use BaseBundle\Entity\User;

class UpdateUserType extends AbstractType
{
    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $this->user;
        $builder
            ->add('fullName', 'text', array('required' => false,
                'label' => 'user.fullName', 'translation_domain' => 'BaseBundle',
                'empty_data' => $user->getFullName(),

            ))
            ->add('email', 'email', array('required' => false,
                'empty_data' => $user->getEmail(),
                'label' => 'user.email', 'translation_domain' => 'BaseBundle',
            ));
    }


    public function getName()
    {
        return 'update_user';
    }
}