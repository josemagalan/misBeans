<?php

namespace BaseBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class SearchGameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('game', 'text', array('required' => true, 'label' => 'form.search', 'translation_domain' => 'BaseBundle'));
    }


    public function getName()
    {
        return 'search_game';
    }
}