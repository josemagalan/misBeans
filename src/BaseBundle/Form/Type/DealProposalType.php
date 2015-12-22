<?php

namespace BaseBundle\Form\Type;

use BaseBundle\Entity\UserPartida;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class DealProposalType
 * @package BaseBundle\Form\Type
 */
class DealProposalType extends AbstractType
{
    protected $userPartida;

    /**
     * DealProposalType constructor.
     * @param UserPartida $userPartida
     */
    public function __construct($userPartida)
    {
        $this->userPartida = $userPartida;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('aluBlancaIn', 'integer', array('required' => true,
                'label' => 'oferta.alu-blanca', 'translation_domain' => 'BaseBundle',
                'scale' => 0,
                'attr' => array('step' => '1',
                    'min' => '0',
                    'max' => '50'),
            ))
            ->add('aluRojaIn', 'integer', array('required' => true,
                'label' => 'oferta.alu-roja', 'translation_domain' => 'BaseBundle',
                'scale' => 0,
                'attr' => array('step' => '1',
                    'min' => '0',
                    'max' => '50'),
            ))
            ->add('aluBlancaOut', 'integer', array('required' => true,
                'label' => 'oferta.alu-blanca', 'translation_domain' => 'BaseBundle',
                'scale' => 0,
                'attr' => array('step' => '1',
                    'min' => '0',
                    'max' => $this->userPartida->getAluBlancaActual()),
            ))
            ->add('aluRojaOut', 'integer', array('required' => true,
                'label' => 'oferta.alu-roja', 'translation_domain' => 'BaseBundle',
                'scale' => 0,
                'attr' => array('step' => '1',
                    'min' => '0',
                    'max' => $this->userPartida->getAluRojaActual()),
            ));
    }

    /**
     * Name of Type
     * @return string
     */
    public function getName()
    {
        return 'deal_proposal';
    }

}