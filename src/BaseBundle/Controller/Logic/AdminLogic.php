<?php

namespace BaseBundle\Controller\Logic;


use BaseBundle\Controller\AdminController;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class AdminLogic
 * @package BaseBundle\Controller\Logic
 */
class AdminLogic extends AdminController
{
    /**
     * Calculates red beans and white beans for each accepted deal.
     * @param ObjectManager $em
     * @param integer $id_partida
     * @return array
     */
    public function barChartGraphics($em, $id_partida)
    {
        $ofertas = $em->getRepository('BaseBundle:Ofertas')->findAllGameDeals($id_partida);
        $ofertasMod = array();
        foreach ($ofertas as $oferta) {
            $tmp = array();
            //dato común
            $tmp['modificado'] = $oferta['modificado'];
            // dato para el grafico de lineas
            $tmp['ratio'] =
                (abs($oferta['aluRojaIn'] - $oferta['aluRojaOut']) / abs($oferta['aluBlancaIn'] - $oferta['aluBlancaOut']));
            //datos para el gráfico de barras
            $tmp['rojas'] = abs($oferta['aluRojaIn'] - $oferta['aluRojaOut']);
            $tmp['blancas'] = abs($oferta['aluBlancaIn'] - $oferta['aluBlancaOut']);
            array_push($ofertasMod, $tmp);
        }
        return $ofertasMod;
    }
}