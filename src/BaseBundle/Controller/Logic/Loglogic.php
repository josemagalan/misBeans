<?php

namespace BaseBundle\Controller\Logic;

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

    /**
     * Obtener Log de un usuario
     *
     * @param int $user_id
     * @param $locale
     * @param ObjectManager $em
     * @return array
     */
    public function getLog($user_id, $locale, $em)
    {
        $logger = array();
        $maxResultados = 15;
        $log = $em->getRepository('BaseBundle:Log')->getUserLog($user_id, $maxResultados);

        foreach ($log as $logData) {
            $time = $logData['fecha']->format('d-m H:i');
            switch ($logData['actionId']) {
                case 1:
                    $nPartida = $em->getRepository('BaseBundle:Partida')->findOneById($logData['actionData']);
                    if ($locale == 'es') {
                        $tmp = $time . ': Te has unido a la partida ' . $nPartida->getNombre();
                    } else {
                        $tmp = $time . ': You have joined ' . $nPartida->getNombre();
                    }
                    array_push($logger, $tmp);
                    break;
                case 2:
                    $username = $em->getRepository('BaseBundle:User')->findOneById($logData['actionData']);
                    if ($locale == 'es') {
                        $tmp = $time . ': Has enviado una oferta a ' . $username->getUsername();
                    } else {
                        $tmp = $time . ': You have sent a deal to ' . $username->getUsername();
                    }
                    array_push($logger, $tmp);
                    break;
                case 3:
                    $username = $em->getRepository('BaseBundle:User')->findOneById($logData['actionData']);
                    if ($locale == 'es') {
                        $tmp = $time . ': Has aceptado una oferta de ' . $username->getUsername();
                    } else {
                        $tmp = $time . ': You have accepted ' . $username->getUsername() . '\'s deal';
                    }
                    array_push($logger, $tmp);
                    break;
                case 4:
                    $username = $em->getRepository('BaseBundle:User')->findOneById($logData['actionData']);
                    if ($locale == 'es') {
                        $tmp = $time . ': Has rechazado una oferta de ' . $username->getUsername();
                    } else {
                        $tmp = $time . ': You have rejected ' . $username->getUsername() . '\'s deal';
                    }
                    array_push($logger, $tmp);
                    break;

                case 5:
                    if ($locale == 'es') {
                        $tmp = $time . ': Has creado una nueva partida';
                    } else {
                        $tmp = $time . ': You have created a new game';
                    }
                    array_push($logger, $tmp);
                    break;
            }
        }
        return $logger;
    }
}