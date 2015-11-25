<?php

namespace BaseBundle\Controller\Logic;


class Loglogic
{

    public function getLog($user_id, $locale, $em){
        $logger = array();
        //Parte de log: 15 resultados
        $log = $em->getRepository('BaseBundle:Log')->getUserLog($user_id, 15);

        foreach ($log as $logData) {
            $time = $logData['fecha']->format('d-m H:i');
            $tmp = '';
            if ($logData['actionId'] == 1) {
                $nPartida = $em->getRepository('BaseBundle:Partida')->findOneById($logData['actionData']);
                if ($locale == 'es') {
                    $tmp = $time . ': Te has unido a la partida ' . $nPartida->getNombre();
                } else {
                    $tmp = $time . ': You have joined ' . $nPartida->getNombre();
                }
                array_push($logger, $tmp);
            }
            if ($logData['actionId'] == 2) {
                $username = $em->getRepository('BaseBundle:User')->findOneById($logData['actionData']);
                if ($locale == 'es') {
                    $tmp = $time . ': Has enviado una oferta a ' . $username->getUsername();
                } else {
                    $tmp = $time . ': You have sent a deal to ' . $username->getUsername();
                }
                array_push($logger, $tmp);
            }
            if ($logData['actionId'] == 3) {
                $username = $em->getRepository('BaseBundle:User')->findOneById($logData['actionData']);
                if ($locale == 'es') {
                    $tmp = $time . ': Has aceptado una oferta de ' . $username->getUsername();
                } else {
                    $tmp = $time . ': You have accepted ' . $username->getUsername() . '\'s deal';
                }
                array_push($logger, $tmp);
            }

            if ($logData['actionId'] == 4) {
                $username = $em->getRepository('BaseBundle:User')->findOneById($logData['actionData']);
                if ($locale == 'es') {
                    $tmp = $time . ': Has rechazado una oferta de ' . $username->getUsername();
                } else {
                    $tmp = $time . ': You have rejected ' . $username->getUsername() . '\'s deal';
                }
                array_push($logger, $tmp);
            }

            if ($logData['actionId'] == 5) {
                if ($locale == 'es') {
                    $tmp = $time . ': Has creado una nueva partida';
                } else {
                    $tmp = $time . ': You have created a new game';
                }
                array_push($logger, $tmp);
            }
        }

        return $logger;
    }
}