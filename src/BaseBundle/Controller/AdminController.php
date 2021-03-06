<?php

namespace BaseBundle\Controller;

use BaseBundle\Controller\Logic\AdminLogic;
use BaseBundle\Controller\Logic\CsvResponse;
use BaseBundle\Controller\Logic\GraphicsLogic;
use BaseBundle\Controller\Logic\Gravatar;
use BaseBundle\Controller\Logic\Loglogic;
use BaseBundle\Controller\Logic\PartidaLogic;
use BaseBundle\Controller\Logic\UserPartidaLogic;
use BaseBundle\Entity\Partida;
use BaseBundle\Form\Type\NewGameType;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\DBAL\Query\QueryBuilder;
use FOS\UserBundle\Model\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Class AdminController
 * @package BaseBundle\Controller
 */
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
        $locale = $request->get('_locale');
        $request->setLocale($locale);
        $request->getSession()->set('_locale', $locale);

        //Security control. Check user roles.
        $response = $this->checkSecurity($request);
        if ($response instanceof RedirectResponse) {
            return $response;
        }
        /** @var User $admin */
        $admin = $this->getUser();
        $gravatar = $this->getGravatar($admin->getEmail());
        $em = $this->getDoctrine()->getManager();
        /** @var Partida $partidas */
        $partidas = $em->getRepository('BaseBundle:Partida')->findAdminPardidas($admin->getId());

        $partidasEnCurso = array();
        $partidasHistorico = array();

        $now = new \DateTime('NOW');

        foreach ($partidas as $partida) {
            /** @var Partida $partida */
            $fin = $partida->getFin();
            if ($now < $fin) {
                $ms = $fin->getTimestamp() * 1000;
                array_push($partidasEnCurso, array('partida' => $partida, 'ms' => $ms));
            } else {
                array_push($partidasHistorico, $partida);
            }
        }
        return $this->render('BaseBundle:Admin:adminHome.html.twig',
            array('partidasEnCurso' => $partidasEnCurso,
                'partidasHistorico' => $partidasHistorico,
                'gravatar' => $gravatar,
            ));

    }

    /**
     * Create a new game
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newGameAction(Request $request)
    {
        $locale = $request->get('_locale');
        $request->setLocale($locale);
        $request->getSession()->set('_locale', $locale);

        //Security control. Check user roles.
        $response = $this->checkSecurity($request);
        if ($response instanceof RedirectResponse) {
            return $response;
        }

        /** @var User $admin */
        $admin = $this->getUser();
        $admin_id = $admin->getId();
        $gravatar = $this->getGravatar($admin->getEmail());
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(new NewGameType());
        $form->handleRequest($request);
        if ($form->isValid()) {
            $data = $form->getData();

            if ($data['fin'] <= new \DateTime('NOW')) {
                $form->get('fin')->addError(new FormError($this->get('translator')->trans('Inserted date is not correct')));
            } elseif ($data['ratio'] <= 0 || $data['ratio'] >= 1) {
                $form->get('ratio')->addError(new FormError($this->get('translator')->trans('Value must be in range (0.01 - 0.99)')));
            } else {
                //Esta correcto -> Guardar partida.
                $result = $em->getRepository('BaseBundle:Partida')->SetNewPartida($data, $admin_id);
                if ($result) {
                    $this->get('session')->getFlashBag()->add('correct', '');
                    $em->getRepository('BaseBundle:Log')->action2log($admin_id, Loglogic::NUEVAPARTIDA, null);
                } else {
                    $this->get('session')->getFlashBag()->add('error', '');
                }
            }
        }
        return $this->render('BaseBundle:Admin:newGame.html.twig',
            array(
                'form' => $form->createView(),
                'gravatar' => $gravatar,
            ));
    }

    /**
     *  Get the ranking and other visual statistics of a game
     *
     * @param Request $request
     * @param int $id_partida
     * @return \Symfony\Component\HttpFoundation\Response|AccessDeniedException
     */
    public function statisticsAction(Request $request, $id_partida)
    {
        $locale = $request->get('_locale');
        $request->setLocale($locale);
        $request->getSession()->set('_locale', $locale);

        //Security control. Check user roles.
        $response = $this->checkSecurity($request);
        if ($response instanceof RedirectResponse) {
            return $response;
        }

        /** @var User $admin */
        $admin = $this->getUser();
        $admin_id = $admin->getId();
        $gravatar = $this->getGravatar($admin->getEmail());
        $em = $this->getDoctrine()->getManager();

        try {
            /** @var Partida $partida */
            $partida = $em->getRepository('BaseBundle:Partida')->isMyAdminGame($id_partida, $admin_id);

            $form = $this->createFormBuilder()->getForm();
            $form->handleRequest($request);
            if ($form->isValid() && $request->isMethod('POST')) {
                $now = new \DateTime('now');
                $partida->setFin($now);
                $em->flush();
            }
            //pasar a ms para Angular
            $fin = $partida->getFin()->getTimestamp() * 1000;

            //la partida está en curso?
            new \DateTime('NOW') >= $partida->getFin() ? $terminado = PartidaLogic::TERMINADO : $terminado = PartidaLogic::ENCURSO;

            $ranking = $em->getRepository('BaseBundle:UserPartida')->getRanking($id_partida);

            $graphics = new GraphicsLogic();
            $adminLogic = new AdminLogic();
            /*-------tarta------*/
            $rankingFutlitidadStats = $graphics->donutJsArray($ranking, 'fUtilidad');
            $rankingAluRojaStats = $graphics->donutJsArray($ranking, 'aluRojaActual');
            $rankingAluBlancaStats = $graphics->donutJsArray($ranking, 'aluBlancaActual');

            /*-------barras en detalle---*/
            $ofertasMod = $adminLogic->barChartGraphics($em, $id_partida);

            //creamos el array para graficar los ratios
            $lineChart = $graphics->linesRatioJsArray($ofertasMod);
            //creamos el array para graficar las alubias
            $barChart = $graphics->barBeansJsArray($ofertasMod);

            return $this->render('BaseBundle:Admin:stats.html.twig',
                array('ranking' => $ranking,
                    'partida' => $partida,
                    'terminado' => $terminado,
                    'fin' => $fin,
                    'rankingFutilidadStats' => $rankingFutlitidadStats,
                    'rankingAluRojaStats' => $rankingAluRojaStats,
                    'rankingAluBlancaStats' => $rankingAluBlancaStats,
                    'lineChart' => $lineChart,
                    'barChart' => $barChart,
                    'gravatar' => $gravatar,
                    'form' => $form->createView(),
                ));
        } catch (\Exception $e) {
            return new AccessDeniedException('You shall not pass!');
        }
    }


    /**
     * Takes the ranking statistics of a game and builds a downloadable csv
     *
     * @param Request $request
     * @return CsvResponse
     */
    public function rankingDownloadAction(Request $request, $id_partida)
    {
        /** @var ObjectManager $em */
        $em = $this->getDoctrine()->getManager();

        try {
            //Security control. Check user roles.
            $response = $this->checkSecurity($request);
            if (!$response instanceof RedirectResponse) {
                /** @var Partida $partida */
                $partida = $em->getRepository('BaseBundle:Partida')->isMyAdminGame($id_partida, $this->getUser()->getId());

                $ranking = $em->getRepository('BaseBundle:UserPartida')->getRanking($id_partida);

                $filename = "Ranking" . $partida->getNombre() . ".csv";
                $response = new CsvResponse($ranking, $filename);
            }
            return $response;
        } catch
        (\Exception $e) {
            return new AccessDeniedException('You shall not pass!');
        }
    }

    /**
     * Takes accepted deals of a game and builds a downloadable csv
     * @param Request $request
     * @param int $id_partida
     * @return CsvResponse|RedirectResponse|AccessDeniedException
     */
    public function dealDownloadAction(Request $request, $id_partida)
    {
        /** @var ObjectManager $em */
        $em = $this->getDoctrine()->getManager();

        try {
            //Security control. Check user roles.
            $response = $this->checkSecurity($request);
            if (!$response instanceof RedirectResponse) {
                /** @var Partida $partida */
                $partida = $em->getRepository('BaseBundle:Partida')->isMyAdminGame($id_partida, $this->getUser()->getId());

                $adminLogic = new AdminLogic();
                $ofertasPartida = $adminLogic->dealsToArray($em, $id_partida);

                $filename = "Ranking" . $partida->getNombre() . ".csv";
                $response = new CsvResponse($ofertasPartida, $filename);
            }

            return $response;
        } catch
        (\Exception $e) {
            return new AccessDeniedException('You shall not pass!');
        }
    }

    /**
     * Distributes randomly the beans to the players, and starts the game.
     *
     * @param Request $request
     * @param int $id_partida
     * @return RedirectResponse|AccessDeniedException
     */
    public function distributeBeansAction(Request $request, $id_partida)
    {
        $locale = $request->get('_locale');
        $request->setLocale($locale);
        $request->getSession()->set('_locale', $locale);

        //Security control. Check user roles.
        $response = $this->checkSecurity($request);
        if ($response instanceof RedirectResponse) {
            return $response;
        }

        $admin_id = $this->getUser()->getId();
        $em = $this->getDoctrine()->getManager();

        try {
            /** @var Partida $partida */
            $partida = $em->getRepository('BaseBundle:Partida')->isMyAdminGame($id_partida, $admin_id);

            /** @var QueryBuilder $qb */
            $qb = $em->createQueryBuilder();
            //localizamos el objeto
            $qb->select('u')
                ->from('BaseBundle:UserPartida', 'u')
                ->where(
                    $qb->expr()->eq('u.idPartida', '?1')
                )
                ->setParameter(1, $id_partida);
            $query = $qb->getQuery();
            $jugadores = $query->getResult();

            $logic = new UserPartidaLogic();
            $logic->distributeBeans($partida, $jugadores, $em);

            //actualizar partida a empezado
            $partida->setEmpezado(true);
            $em->flush();

            return new RedirectResponse($this->get('router')->generate('game_statistics', array('id_partida' => $id_partida)));

        } catch (\Exception $e) {
            return new AccessDeniedException('You shall not pass!');
        }
    }

    /**
     * Check User roles. User must be admin or superAdmin
     * @param Request $request
     * @return RedirectResponse
     * @internal param $securityContext
     */
    private function checkSecurity(Request $request)
    {
        $securityContext = $this->get('security.authorization_checker');
        if (false === $securityContext->isGranted('ROLE_ADMIN') && false === $securityContext->isGranted('ROLE_SUPER_ADMIN')) {
            return new RedirectResponse($this->container->get('router')->generate('base_homepage'));
        }
    }

    /**
     * Gets the gravatar URL for an email
     *
     * @param String $email
     * @return String
     */
    protected function getGravatar($email)
    {
        $grav = new Gravatar($email);
        $gravatar = $grav->get_gravatar();
        return $gravatar;
    }
}