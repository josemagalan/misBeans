<?php

namespace BaseBundle\Controller\Logic;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Adds extra functions to calculate beans and utility function
 *
 * Class PartidaLogic
 * @package BaseBundle\Controller\Logic
 */
class PartidaLogic extends Controller
{
    /**
     * Calculates number of beans based on Alg_reparto of Partida
     *
     * @param $idPartida
     * @param $algReparto
     * @return array
     */
    public function calculateBeans($idPartida, $algReparto)
    {
        $aluRojaInicial = 10;
        $aluBlancaInicial = 15;

        $alubias = array('aluRojaInicial' => $aluRojaInicial, 'aluBlancaInicial' => $aluBlancaInicial);
        return $alubias;
    }

    /**
     * Calculates utility function based on algUtilidad of Partida
     *
     * @param $aluRojaActual
     * @param $aluBlancaActual
     * @param $algUtilidad
     * @return int
     */
    public function calculateFUtilidad($aluRojaActual, $aluBlancaActual, $algUtilidad)
    {
        $fUtilidad = 0;

        if ($algUtilidad == 1) {
            $fUtilidad = $aluRojaActual * $aluBlancaActual;
        }
        return $fUtilidad;
    }
}

