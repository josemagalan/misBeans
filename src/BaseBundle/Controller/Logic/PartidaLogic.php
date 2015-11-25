<?php

namespace BaseBundle\Controller\Logic;

use BaseBundle\Controller\PartidaController;

/**
 * Adds extra functions to calculate beans and utility function
 *
 * Class PartidaLogic
 * @package BaseBundle\Controller\Logic
 */
class PartidaLogic extends PartidaController
{

    public function newPlayer($partida, $user_id, $em)
    {
        $id_partida = $partida->getId();
        $em->getRepository('BaseBundle:UserPartida')->addJugador($user_id, $id_partida);
        //log ingresa en partida --> 1
        $em->getRepository('BaseBundle:Log')->action2log($user_id, 1, $id_partida);

        return new RedirectResponse($this->get('router')->generate('partida_home', array('id_partida' => $id_partida)));
    }

    /**
     * Distributes the beans among the players.
     *
     * @param $partidaData
     * @param $jugadores
     * @param $em
     */
    public function distributeBeansLogic($partidaData, $jugadores, $em)
    {
        $data = $this->calculateBeans($partidaData, count($jugadores));

        //array con alubias rojas y blancas
        $alubiasArray = array();
        for ($i = 1; $i <= $data['totales']; $i++) {
            $letra = null;
            if ($i <= $data['aluBlanca']) {
                $letra = 'B';
            } else {
                $letra = 'R';
            }
            array_push($alubiasArray, $letra);
        }

        //aleatorizar array
        shuffle($alubiasArray);
        //dividimos el array en montones de "alubias por usuario" alubias
        $alubiasArray = array_chunk($alubiasArray, $partidaData->getAluPorUsuario());

        for ($i = 0; $i < count($jugadores); $i++) {
            $rojas = count(array_keys($alubiasArray[$i], 'R'));
            $blancas = count(array_keys($alubiasArray[$i], 'B'));
            // calcular utilidad dadas las alubias
            $fUtilidad = $this->calculateFUtilidad($rojas, $blancas, $partidaData);
            // Asignar a cada jugador sus alubias y función de utilidad
            $em->getRepository('BaseBundle:UserPartida')->distributeBeans($jugadores[$i]->getIdUser(),
                $partidaData->getId(), $rojas, $blancas, $fUtilidad);
        }
    }


    /**
     * Calculates number of beans based on  of Partida
     *
     * @param $partidaData
     * @return array
     */
    public function calculateBeans($partidaData, $nJugadores)
    {
        $alubiasTot = $nJugadores * $partidaData->getAluPorUsuario();
        $aluRoja = $partidaData->getRatio() * $alubiasTot;
        $aluBlanca = (1 - $partidaData->getRatio()) * $alubiasTot;

        $alubias = array('aluRoja' => $aluRoja, 'aluBlanca' => $aluBlanca,
            'totales' => $alubiasTot);
        return $alubias;
    }

    /**
     * Calculates utility function based on Cobb Douglas exponents of Partida
     *
     * @param $aluRojaActual
     * @param $aluBlancaActual
     * @param $partida
     * @return int
     */
    public function calculateFUtilidad($aluRojaActual, $aluBlancaActual, $partida)
    {
        $expY = $partida->getExpY();
        $expZ = $partida->getExpZ();

        $fUtilidad = pow($aluBlancaActual, $expY) * pow($aluRojaActual, $expZ);

        return $fUtilidad;
    }
}

