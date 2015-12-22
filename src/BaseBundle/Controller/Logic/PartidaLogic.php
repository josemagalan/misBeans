<?php

namespace BaseBundle\Controller\Logic;

use BaseBundle\Controller\PartidaController;
use BaseBundle\Entity\Ofertas;
use BaseBundle\Entity\Partida;
use BaseBundle\Entity\User;
use BaseBundle\Entity\UserPartida;
use Doctrine\Common\Persistence\ObjectManager;
use DateInterval;

/**
 * Adds extra functions to calculate beans and utility function
 *
 * Class PartidaLogic
 * @package BaseBundle\Controller\Logic
 */
class PartidaLogic extends PartidaController
{
    const ENCURSO = 0;
    const TERMINADO = 1;
    const OFERTASSINLIMITE = 0;

    /**
     * Add a player to a game
     *
     * @param Partida $partida
     * @param int $user_id
     * @param ObjectManager $em
     */
    public function newPlayer($partida, $user_id, $em)
    {
        $id_partida = $partida->getId();
        $em->getRepository('BaseBundle:UserPartida')->addJugador($user_id, $id_partida);
        $em->getRepository('BaseBundle:Log')->action2log($user_id, Loglogic::INGRESARENPARTIDA, $id_partida);
    }


    /**
     * Calculates number of beans based on  players registered in a game
     *
     * @param Partida $partidaData
     * @param int $nJugadores
     * @return array
     */
    public function calculateBeans($partidaData, $nJugadores)
    {
        $alubiasTot = $nJugadores * $partidaData->getAluPorUsuario();
        $aluRoja = $partidaData->getRatio() * $alubiasTot;
        $aluBlanca = (1 - $partidaData->getRatio()) * $alubiasTot;

        $alubias = array('aluRoja' => $aluRoja, 'aluBlanca' => $aluBlanca,
            'total' => $alubiasTot);
        return $alubias;
    }

    /**
     * Calculates utility function based on Cobb Douglas exponents of Partida
     *
     * @param int $aluRojaActual
     * @param int $aluBlancaActual
     * @param Partida $partida
     * @return int
     */
    public function calculateFUtilidad($aluRojaActual, $aluBlancaActual, $partida)
    {
        $expY = $partida->getExpY();
        $expZ = $partida->getExpZ();

        $fUtilidad = pow($aluBlancaActual, $expY) * pow($aluRojaActual, $expZ);

        return $fUtilidad;
    }

    /**
     * Evolution of beans and utility function in the game
     *
     * @param ObjectManager $em
     * @param int $user_id
     * @param Partida $partida
     * @return array
     */
    public function partidaEvolution($em, $user_id, $partida)
    {
        $userPartidaLogic = new UserPartidaLogic();
        $ofertasPartida = array(); //ofertas aceptadas en una partida (tanto de las enviadas como recividas)


        $ofertas = $em->getRepository('BaseBundle:Ofertas')->findAllUserGameDeals($user_id, $partida->getId());
        foreach ($ofertas as $oferta) {
            /** @var Ofertas $oferta */
            //calcular alubias en funcion de si oferta enviada o recibida
            if ($oferta->getIdCreador()->getId() == $user_id) {
                $tmp = $userPartidaLogic->beansStatus($oferta, 1);
            } else {
                $tmp = $userPartidaLogic->beansStatus($oferta, 2);
            }
            $tmp['modificado'] = $oferta->getModificado();
            array_push($ofertasPartida, $tmp);
        }

        /** @var UserPartida $userpartida */
        $userpartida = $em->getRepository('BaseBundle:UserPartida')->findByIDS($user_id, $partida->getId());
        $evolucion = array();

        $a_rojas_tmp = $userpartida->getAluRojaInicial();
        $a_blancas_tmp = $userpartida->getAluBlancaInicial();
        foreach ($ofertasPartida as $oferta) {
            $oferta['aluRoja'] += $a_rojas_tmp;
            $oferta['aluBlanca'] += $a_blancas_tmp;
            $oferta['fUtilidad'] = $this->calculateFUtilidad($oferta['aluRoja'], $oferta['aluBlanca'], $partida);
            $a_blancas_tmp = $oferta['aluBlanca'];
            $a_rojas_tmp = $oferta['aluRoja'];
            array_push($evolucion, $oferta);
        }

        return $evolucion;
    }

    /**
     * Checks if a deal is in progress; if not changes it status
     *
     * @param array $ofertas
     * @param ObjectManager $em
     * @return array
     */
    public function checkInProgress(array $ofertas, $em)
    {
        $oferta_enCurso = array();
        $now = new \DateTime('NOW');

        foreach ($ofertas as $oferta) {
            $intervalo = $oferta['tiempoOferta'];
            $intervalo_string = 'PT' . $intervalo . 'M';
            $fin = $oferta['creado'];
            $fin->add(new DateInterval($intervalo_string));

            if ($now < $fin) {
                array_push($oferta_enCurso, $oferta);
            } else {
                $em->getRepository('BaseBundle:Ofertas')->updateStatus(OfertaLogic::CONCLUIDA, $oferta['id']);
            }
        }
        return $oferta_enCurso;
    }


}

