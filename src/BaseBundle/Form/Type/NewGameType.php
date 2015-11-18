<?php


namespace BaseBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
                    'min' => '0',
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
                    'min' => '0',
                    'max' => '720'),
            ))
            ->add('algUtilidad', 'choice', array(
                'label' => 'partida.alg-utilidad', 'translation_domain' => 'BaseBundle',
                'choice_list' => new ChoiceList(
                    array(1,),
                    array('partida.algutilidad.1',)
                )
            ))
            ->add('algReparto', 'choice', array(
                'label' => 'partida.alg-reparto', 'translation_domain' => 'BaseBundle',
                'choice_list' => new ChoiceList(
                    array(1,),
                    array('partida.algreparto.1',)
                )
            ))
            ->add('aluRoja', 'integer', array('required' => true,
                'label' => 'partida.alu-roja', 'translation_domain' => 'BaseBundle',
                'scale' => 0,
                'attr' => array('step' => '1',
                    'min' => '0'),
            ))
            ->add('aluBlanca', 'integer', array('required' => true,
                'label' => 'partida.alu-blanca', 'translation_domain' => 'BaseBundle',
                'scale' => 0,
                'attr' => array('step' => '1',
                    'min' => '0'),
            ))
        ;
    }

    /*public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'BaseBundle\Entity\Partida'
        ));
    }*/

    public function getName()
    {
        return 'new_game';
    }
}