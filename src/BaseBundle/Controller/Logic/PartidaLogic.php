<?php

namespace BaseBundle\Controller\Logic;

use BaseBundle\Controller\PartidaController;
use BaseBundle\Entity\Partida;
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
    const ENCURSO =0;
    const TERMINADO = 1;

    /**
     * Add a player to a game
     *
     * @param Partida $partida
     * @param int $user_id
     * @param ObjectManager $em
     * @return RedirectResponse
     */
    public function newPlayer($partida, $user_id, $em)
    {
        $id_partida = $partida->getId();
        $em->getRepository('BaseBundle:UserPartida')->addJugador($user_id, $id_partida);
        $em->getRepository('BaseBundle:Log')->action2log($user_id, Loglogic::INGRESARENPARTIDA, $id_partida);

        return new RedirectResponse($this->get('router')->generate('partida_home', array('id_partida' => $id_partida)));
    }

    /**
     * Distributes the beans among the players.
     *
     * @param Partida $partidaData
     * @param array $jugadores
     * @param ObjectManager $em
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

        shuffle($alubiasArray);
        //dividimos el array en montones de "alubias por usuario" alubias
        $alubiasArray = array_chunk($alubiasArray, $partidaData->getAluPorUsuario());

        for ($i = 0; $i < count($jugadores); $i++) {
            $rojas = count(array_keys($alubiasArray[$i], 'R'));
            $blancas = count(array_keys($alubiasArray[$i], 'B'));
            $fUtilidad = $this->calculateFUtilidad($rojas, $blancas, $partidaData);
            // Asignar a cada jugador sus alubias y función de utilidad
            $em->getRepository('BaseBundle:UserPartida')->distributeBeans($jugadores[$i]->getIdUser(),
                $partidaData->getId(), $rojas, $blancas, $fUtilidad);
        }
    }


    /**
     * Calculates number of beans based on  of Partida
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
            'totales' => $alubiasTot);
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
     * Evolution of beans and utility Funtion in the game
     *
     * @param ObjectManager $em
     * @param int $user_id
     * @param Partida $partida
     * @return array
     */
    public function partidaGraphic($em, $user_id, $partida)
    {
        $graphics = new GraphicsLogic();
        $ofertasPartida = array(); //ofertas aceptadas en una partida (tanto de las enviadas como recividas)

        //calcular alubias en funcion de quien ha enviado la oferta
        //jugador envía la oferta
        $ofertasOut = $em->getRepository('BaseBundle:Ofertas')->findSentOffers($user_id, $partida->getId(), OfertaLogic::ACEPTADA);
        foreach ($ofertasOut as $oferta) {
            $tmp = $graphics->beansStatus($oferta, 1);
            $tmp['modificado'] = $oferta['modificado'];
            array_push($ofertasPartida, $tmp);
        }
        //Jugador recibe la oferta
        $ofertasOut = $em->getRepository('BaseBundle:Ofertas')->findRecievedOffers($user_id, $partida->getId(), OfertaLogic::ACEPTADA);
        foreach ($ofertasOut as $oferta) {
            $tmp = $graphics->beansStatus($oferta, 2);
            $tmp['modificado'] = $oferta['modificado'];
            array_push($ofertasPartida, $tmp);
        }

        // sabemos las alubias de cada transaccion. Recorremos y calculamos la evolución a lo largo de la partida.
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

