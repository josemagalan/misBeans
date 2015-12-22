<?php

namespace BaseBundle\Controller\Logic;

use BaseBundle\Entity\Log;
use BaseBundle\Entity\Partida;
use BaseBundle\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Translation\Translator;

/**
 * Class Loglogic
 * @package BaseBundle\Controller\Logic
 */
class Loglogic
{
    const INGRESARENPARTIDA = 1;
    const NUEVAOFERTA = 2;
    const ACEPTAROFERTA = 3;
    const RECHAZAROFERTA = 4;
    const NUEVAPARTIDA = 5;
    private $maxResultados = 15;

    /**
     * Obtener Log de un usuario
     *
     * @param int $user_id
     * @param ObjectManager $em
     * @param Translator $translator
     * @return array
     */
    public function getLog($user_id, $em, $translator)
    {
        $logger = array();
        /** @var Log $log */
        $log = $em->getRepository('BaseBundle:Log')->getUserLog($user_id, $this->maxResultados);

        foreach ($log as $logData) {
            /** @var Log $logData */
            $time = $logData->getFecha()->format('d-m H:i');
            switch ($logData->getActionId()) {
                case Loglogic::INGRESARENPARTIDA:
                    /** @var Partida $partida */
                    $partida = $em->getRepository('BaseBundle:Partida')->findOneById($logData->getActionData());
                    $tmp = $time . ': ' . $translator->trans('You have joined ') . $partida->getNombre();
                    array_push($logger, $tmp);
                    break;
                case Loglogic::NUEVAOFERTA:

                    /** @var User $username */
                    $username = $em->getRepository('BaseBundle:User')->findOneById($logData->getActionData());
                    $tmp = $time . ': ' . $translator->trans('You have sent a deal to ') . $username->getUsername();
                    array_push($logger, $tmp);
                    break;
                case Loglogic::ACEPTAROFERTA:
                    /** @var User $username */
                    $username = $em->getRepository('BaseBundle:User')->findOneById($logData->getActionData());
                    $tmp = $time . ': ' . $translator->trans(
                            'You have accepted %username%\'s deal', array('%username%' => $username->getUsername()));
                    array_push($logger, $tmp);
                    break;
                case Loglogic::RECHAZAROFERTA:
                    /** @var User $username */
                    $username = $em->getRepository('BaseBundle:User')->findOneById($logData->getActionData());
                    $tmp = $time . ': ' . $translator->trans(
                            'You have rejected %username%\'s deal', array('%username%' => $username->getUsername()));
                    array_push($logger, $tmp);
                    break;

                case Loglogic::NUEVAPARTIDA:
                    $tmp = $time . ': ' . $translator->trans('You have created a new game');
                    array_push($logger, $tmp);
                    break;
            }
        }
        return $logger;
    }

    /**
     * Get resultados
     * @return int
     */
    public function getMaxResultados()
    {
        return $this->maxResultados;
    }

    /**
     * Set resultados
     * @param int $maxResultados
     */
    public function setMaxResultados($maxResultados)
    {
        $this->maxResultados = $maxResultados;
    }
}