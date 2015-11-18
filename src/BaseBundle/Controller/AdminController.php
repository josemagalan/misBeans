<?php

namespace BaseBundle\Controller;

use BaseBundle\Controller\Logic\CsvResponse;
use BaseBundle\Controller\Logic\GraphicsLogic;
use BaseBundle\Form\Type\NewGameType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AdminController extends Controller
{

    /**
     * Gets the home for admin user
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function adminHomeAction(Request $request)
    {
        //set language
        $locale = $request->get('_locale');
        $request->setLocale($locale);
        $request->getSession()->set('_locale', $locale);

        //Security control. Check user roles. If role is not ROLE_ADMIN -> redirect to homepage
        $this->checkSecurity($this->container->get('security.context'));

        //get User info
        $admin = $this->get('security.context')->getToken()->getUser();
        $admin_id = $admin->getId();

        //Get Entity manager
        $em = $this->getDoctrine()->getManager();
        //Partidas creadas por ese administrador
        $partidas = $em->getRepository('BaseBundle:Partida')->findAdminPardidas($admin_id);

        $partidasEnCurso = array();
        $partidasHistorico = array();

        $now = new \DateTime('NOW');

        foreach ($partidas as $partida) {

            $dateBD = $partida['creado'];
            $fin = $partida['fin'];

            if ($now < $fin) {
                $ms = $fin->getTimestamp() * 1000;
                $partida['ms'] = $ms;
                //$partida['intervalo'] = date_diff($now, $fin);
                array_push($partidasEnCurso, $partida);
            } else {
                array_push($partidasHistorico, $partida);
            }
        }

        return $this->render('BaseBundle:Admin:adminHome.html.twig',
            array('partidasEnCurso' => $partidasEnCurso, 'partidasHistorico' => $partidasHistorico));

    }

    /**
     * Check User roles. User must be admin or superAdmin
     * @param $securityContext
     * @return RedirectResponse
     */
    protected function checkSecurity($securityContext)
    {
        if (!$securityContext->isGranted('ROLE_ADMIN') && !$securityContext->isGranted('ROLE_SUPER_ADMIN')) {
            return new RedirectResponse($this->container->get('router')->generate('base_homepage'));
        }
    }

    /**
     * Create a new game
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newGameAction(Request $request)
    {
        //set language
        $locale = $request->get('_locale');
        $request->setLocale($locale);
        $request->getSession()->set('_locale', $locale);

        //create form
        $form = $this->createForm(new NewGameType());
        $form->handleRequest($request);
        if ($form->isValid()) {
            $data = $form->getData();
            // ¿La fecha que introducida es correcta?
            if ($data['fin'] <= new \DateTime('NOW')) {
                if ($locale == 'es') {
                    $form->get('fin')->addError(new FormError('La fecha introducida es incorrecta'));
                } else {
                    $form->get('fin')->addError(new FormError('The entered date is incorrect'));
                }
                return $this->render('BaseBundle:Admin:newGame.html.twig',
                    array(
                        'form' => $form->createView(),
                    ));
            }

            $admin_id = $this->get('security.context')->getToken()->getUser()->getId();
            $em = $this->getDoctrine()->getManager();
            //Esta correcto -> Guardar partida.
            $result = $em->getRepository('BaseBundle:Partida')->SetNewPartida($data, $admin_id);

            if ($result) {
                $this->get('session')->getFlashBag()->add(
                    'correct', '');
                $em->getRepository('BaseBundle:Log')->action2log($admin_id, 5, null);
            } else {
                $this->get('session')->getFlashBag()->add(
                    'error', '');
            }
        }
        return $this->render('BaseBundle:Admin:newGame.html.twig',
            array(
                'form' => $form->createView(),
            ));
    }

    /**
     *  Get the ranking and other visual statistics of a game
     *
     * @param Request $request
     * @param $id_partida
     * @return \Symfony\Component\HttpFoundation\Response|AccessDeniedException
     */
    public function statisticsAction(Request $request, $id_partida)
    {
        //set language
        $locale = $request->get('_locale');
        $request->setLocale($locale);
        $request->getSession()->set('_locale', $locale);

        //Security control. Check user roles. If role is not ROLE_ADMIN -> redirect to homepage
        $this->checkSecurity($this->container->get('security.context'));

        //get User info
        $admin = $this->get('security.context')->getToken()->getUser();
        $admin_id = $admin->getId();

        //Get Entity manager
        $em = $this->getDoctrine()->getManager();

        //Check: Yo soy el creador de la partida y tengo acceso
        $partidaInfo = $em->getRepository('BaseBundle:Partida')->isMyAdminGame($id_partida, $admin_id);
        if (!count($partidaInfo) > 0) {
            return new AccessDeniedException('You shall not pass!');
        }
        //arrayList to array
        $partidaInfo = $partidaInfo[0];
        //pasar a ms el fin de la partida y guardarlo (Angular usa esa variable)
        $fin = $partidaInfo['fin'];
        $ms = $fin->getTimestamp() * 1000;
        $partidaInfo['ms'] = $ms;

        //la partida está en curso?
        $now = new \DateTime('NOW');
        $intervalo = date_diff($now, $fin);
        $now >= $fin ? $partidaInfo['terminado'] = 1 : $partidaInfo['terminado'] = 0;

        $ranking = $em->getRepository('BaseBundle:Jugadores')->getRanking($id_partida);

        // Get session
        $session = $this->container->get('session');
        $session->set('ranking', $ranking);
        $session->set('Pnombre', $partidaInfo['nombre']);

        //Generar gráficos
        $graphics = new GraphicsLogic();
        /*-------tarta------*/
        $rankingFutlitidadStats = $graphics->donutJsArray($ranking, 'fUtilidad');
        $rankingAluRojaStats = $graphics->donutJsArray($ranking, 'aluRojaActual');
        $rankingAluBlancaStats = $graphics->donutJsArray($ranking, 'aluBlancaActual');

        /*-------barras en detalle---*/
        $ofertas = $em->getRepository('BaseBundle:Ofertas')->findAllGameDeals($id_partida);
        $ofertasMod = array();
        foreach ($ofertas as $oferta) {
            $tmp = array();
            //dato común
            $tmp['modificado'] = $oferta['modificado'];
            // dato para el grafico de lineas
            $tmp['ratio'] =
                (abs($oferta['aluBlancaIn'] - $oferta['aluBlancaOut']) / abs($oferta['aluRojaIn'] - $oferta['aluRojaOut']));
            //datos para el gráfico de barras
            $tmp['rojas'] = abs($oferta['aluRojaIn'] - $oferta['aluRojaOut']);
            $tmp['blancas'] = abs($oferta['aluBlancaIn'] - $oferta['aluBlancaOut']);
            array_push($ofertasMod, $tmp);
        }
        //creamos el array para graficar los ratios
        $lineChart = $graphics->linesRatioJsArray($ofertasMod);
        //creamos el array para graficar las alubias
        $barChart = $graphics->barBeansJsArray($ofertasMod);

        return $this->render('BaseBundle:Admin:stats.html.twig',
            array('ranking' => $ranking,
                'partida' => $partidaInfo,
                'rankingFutilidadStats' => $rankingFutlitidadStats,
                'rankingAluRojaStats' => $rankingAluRojaStats,
                'rankingAluBlancaStats' => $rankingAluBlancaStats,
                'lineChart' => $lineChart,
                'barChart' => $barChart,
            ));
    }


    /**
     * Takes the ranking statistics of a game and builds a downloadable csv
     *
     * @return CsvResponse
     */
    public function rankingDownloadAction()
    {

        //Security control. Check user roles. If role is not ROLE_ADMIN -> redirect to homepage
        $this->checkSecurity($this->container->get('security.context'));

        // Get session
        $session = $this->container->get('session');

        $ranking = $session->get('ranking');
        $nombre = $session->get('Pnombre');
        $filename = "Ranking" . $nombre . ".csv";

        //Export data to csv
        $response = new CsvResponse($ranking, $filename);

        return $response;
    }
}