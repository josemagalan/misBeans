<?php


namespace BaseBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;


class NewGameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre', 'text', array('required' => true, 'label' => 'partida.nombre', 'translation_domain' => 'BaseBundle'))
            ->add('password', 'password', array('required' => false, 'label' => 'partida.password', 'translation_domain' => 'BaseBundle'))
            ->add('fin', 'datetime', array('required' => true,
                'label' => 'partida.fin', 'translation_domain' => 'BaseBundle',
                'date_widget' => "single_text",
                'time_widget' => "single_text"))
            ->add('maxJugadores', 'integer', array('required' => true,
                'label' => 'partida.max-jugadores', 'translation_domain' => 'BaseBundle',
                'scale' => 0,
                'attr' => array('step' => '1',
                    'min' => '1',
                    'max' => '500'),
            ))
            ->add('maxOfertas', 'integer', array('required' => true,
                'label' => 'partida.max-ofertas', 'translation_domain' => 'BaseBundle',
                'scale' => 0,
                'attr' => array('step' => '1',
                    'min' => '0'),
            ))
            ->add('tiempoOferta', 'integer', array('required' => true,
                'label' => 'partida.tiempo-oferta', 'translation_domain' => 'BaseBundle',
                'scale' => 0,
                'attr' => array('step' => '1',
                    'min' => '1',
                    'max' => '720'),
            ))
            ->add('aluPUsuario', 'integer', array('required' => true,
                'label' => 'partida.aluPUsuario', 'translation_domain' => 'BaseBundle',
                'scale' => 0,
                'attr' => array('step' => '1',
                    'min' => '1'),
            ))
            //proporcion de alubia roja sobre blanca
            ->add('ratio', 'number', array('required' => true,
                'label' => 'partida.proportion', 'translation_domain' => 'BaseBundle',
                'scale' => 2,
                'attr' => array('step' => '0.01',
                    'min' => '0.01',
                    'max' => '0.99'),
            ))
            ->add('expY', 'integer', array('required' => true,
                'label' => 'partida.expY', 'translation_domain' => 'BaseBundle',
                'scale' => 0,
                'attr' => array('step' => '1',
                    'min' => '1'),
            ))
            ->add('expZ', 'integer', array('required' => true,
                'label' => 'partida.expZ', 'translation_domain' => 'BaseBundle',
                'scale' => 0,
                'attr' => array('step' => '1',
                    'min' => '1'),
            ));
    }

    public function getName()
    {
        return 'new_game';
    }
}