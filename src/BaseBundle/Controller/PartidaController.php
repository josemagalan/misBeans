<?php

namespace BaseBundle\Controller;

use BaseBundle\Controller\Logic\GraphicsLogic;
use BaseBundle\Form\Type\AcceptRejectDealType;
use BaseBundle\Form\Type\DealProposalType;
use BaseBundle\Form\Type\DeleteDealType;
use DateInterval;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class PartidaController extends Controller
{

    /**
     * Register an user in a game -> requires password only in case admin requires it.
     * @param Request $request
     * @param $id_partida
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function partidaRegisterAction(Request $request, $id_partida)
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'Unable to access this page!');

        //set language
        $locale = $request->get('_locale');
        $request->setLocale($locale);
        $request->getSession()->set('_locale', $locale);

        //get User info
        $user = $this->get('security.context')->getToken()->getUser();
        $user_id = $user->getId();

        //Get Entity Manager
        $em = $this->getDoctrine()->getManager();

        $jugador = $em->getRepository('BaseBundle:Partida')->findPartidaInfo($user_id, $id_partida);
        if (count($jugador) > 0) {
            return new RedirectResponse($this->get('router')->generate('partida_home', array('id_partida' => $id_partida)));
        } else {
            $partida = $em->getRepository('BaseBundle:Partida')->findOneById($id_partida);
            if ($partida->getPassword() == null) {
                $this->newPlayer($partida, $user_id);
            } else {
                $form = $this->createFormBuilder()
                    ->add('password', 'password', array('required' => true, 'label' => 'partida.password', 'translation_domain' => 'BaseBundle'))
                    ->getForm();

                $form->handleRequest($request);
                if ($form->isValid() && $request->isMethod('POST')) {
                    $data = $form->getData();
                    if (strcmp($data['password'], $partida->getPassword()) == 0) {
                        $this->newPlayer($partida, $user_id);
                    } else {
                        if ($locale == 'es') {
                            $form->get('password')->addError(new FormError('Contraseña incorrecta'));
                        } else {
                            $form->get('fin')->addError(new FormError('Incorrect password'));
                        }
                    }
                }
            }
        }
        return $this->render('BaseBundle:Partida:registerP.html.twig',
            array('partida' => $partida,
                'form' => $form->createView(),
            ));
    }

    protected function newPlayer($partida, $user_id)
    {
        //Get Entity Manager
        $em = $this->getDoctrine()->getManager();

        $id_partida = $partida->getId();
        $em->getRepository('BaseBundle:Jugadores')->addJugador($user_id, $id_partida);
        //log ingresa en partida --> 1
        $em->getRepository('BaseBundle:Log')->action2log($user_id, 1, $id_partida);

        return new RedirectResponse($this->get('router')->generate('partida_home', array('id_partida' => $id_partida)));
    }

    /**
     * Controller of games main page
     *
     * @param Request $request
     * @param $id_partida
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function partidaAction(Request $request, $id_partida)
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'Unable to access this page!');

        //set language
        $locale = $request->get('_locale');
        $request->setLocale($locale);
        $request->getSession()->set('_locale', $locale);

        //Get router
        $router = $this->container->get('router');

        //get User info
        $user = $this->get('security.context')->getToken()->getUser();
        $user_id = $user->getId();

        //Get Entity Manager
        $em = $this->getDoctrine()->getManager();
        $partida = $em->getRepository('BaseBundle:Partida')->findPartidaInfo($user_id, $id_partida);

        if (count($partida) > 0 && new \DateTime('NOW') < $partida[0]['fin']) { //El jugador está registrado en la partida

            //consultar el resto de jugadores
            $jugadores = $em->getRepository('BaseBundle:Jugadores')->findAllFriends($id_partida);

            //consultar ofertas pendientes
            $oferta_recibida = $em->getRepository('BaseBundle:Ofertas')->findRecievedOffers($user_id);
            $oferta_recibida_enCurso = $this->checkInProgress($oferta_recibida);

            //consultar ofertas enviadas en curso.
            $oferta_enviada = $em->getRepository('BaseBundle:Ofertas')->findSentOffers($user_id);
            $oferta_enviada_enCurso = $this->checkInProgress($oferta_enviada);

            $acceptReject = $this->createForm(new AcceptRejectDealType());
            $delForm = $this->createForm(new DeleteDealType());

            //guardar en sesion el valor de partida
            $session = $this->container->get('session');
            $session->set('id_partida', $id_partida);

            /* Grafica de linea para usuario */
            //Obtenemos la instancia de la clase en las que están las funcionas de graficar
            $graphics = new GraphicsLogic();
            $ofertaEvolucion = array();
            //calcular alubias en funcion de quien ha enviado la oferta
            //jugador envía la oferta
            $ofertasOut = $em->getRepository('BaseBundle:Ofertas')->dealsInfoSent($id_partida, $user_id);
            foreach ($ofertasOut as $oferta) {
                $tmp = $graphics->beansStatus($oferta, 1);
                $tmp['modificado'] = $oferta['modificado'];
                array_push($ofertaEvolucion, $tmp);
            }
            //Jugador recibe la oferta
            $ofertasOut = $em->getRepository('BaseBundle:Ofertas')->dealsInfoRecieved($id_partida, $user_id);
            foreach ($ofertasOut as $oferta) {
                $tmp = $graphics->beansStatus($oferta, 2);
                $tmp['modificado'] = $oferta['modificado'];
                array_push($ofertaEvolucion, $tmp);
            }
            //creamos el array para graficar
            $lineChart = $graphics->linesUserJsArray($ofertaEvolucion);

            return $this->render('BaseBundle:Partida:partida.html.twig',
                array('partida' => $partida,
                    'jugadores' => $jugadores,
                    'user_id' => $user_id,
                    'oferta_recibida' => $oferta_recibida_enCurso,
                    'oferta_enviada' => $oferta_enviada_enCurso,
                    'acceptReject' => $acceptReject->createView(),
                    'delForm' => $delForm->createView(),
                    'lineChart' => $lineChart,
                ));
        }
        return new RedirectResponse($router->generate('base_homepage'));
    }

    /**
     * Checks if a deal is in progress; if not changes it status
     *
     * @param array $ofertas
     * @return array
     */
    protected function checkInProgress(array $ofertas)
    {
        //Get Entity Manager
        $em = $this->getDoctrine()->getManager();
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
                // modificar estado partida a 3
                $em->getRepository('BaseBundle:Ofertas')->updateStatus(3, $oferta['id']);
            }
        }
        return $oferta_enCurso;
    }

    /**
     * Accept or reject a deal
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function AcceptRejectAction(Request $request)
    {
        //Get Entity Manager
        $em = $this->getDoctrine()->getManager();

        $acceptReject = $this->createForm(new AcceptRejectDealType());

        $acceptReject->handleRequest($request);
        if ($request->isMethod('POST')) {
            $data = $acceptReject->getData();

            $oferta = $em->getRepository('BaseBundle:Ofertas')->
            checkDeal($data['idC'], $data['idP'], $data['idO'], $data['idD']);
            if (count($oferta) > 0) { //Los datos de la oferta son correctos
                if ($acceptReject->get('accept')->isClicked()) {
                    $oferta = $em->getRepository('BaseBundle:Ofertas')->findOneById($data['idO']);
                    $partida = $em->getRepository('BaseBundle:Partida')->findOneById($data['idP']);
                    $alg_utilidad = $partida->getAlgUtilidad();

                    $aluBlanca = $oferta->getAluBlancaIn() - $oferta->getAluBlancaOut();
                    $aluRoja = $oferta->getAlurojaIn() - $oferta->getAluRojaOut();
                    $resultado1 = $em->getRepository('BaseBundle:Jugadores')->
                    updateBeans($data['idC'], $data['idP'], $aluRoja, $aluBlanca, $alg_utilidad);

                    $aluBlanca = $oferta->getAluBlancaOut() - $oferta->getAluBlancaIn();
                    $aluRoja = $oferta->getAluRojaOut() - $oferta->getAlurojaIn();
                    $resultado2 = $em->getRepository('BaseBundle:Jugadores')->
                    updateBeans($data['idD'], $data['idP'], $aluRoja, $aluBlanca, $alg_utilidad);

                    $resultado3 = $em->getRepository('BaseBundle:Ofertas')->updateStatus(1, $data['idO']);
                    //log de la operacion
                    $em->getRepository('BaseBundle:Log')->action2log($data['idD'], 3, $data['idC']);
                    if ($resultado1 && $resultado2 && $resultado3) {
                        $this->get('session')->getFlashBag()->add(
                            'correct', '');
                    } else {
                        $this->get('session')->getFlashBag()->add(
                            'error', '');
                    }

                } elseif ($acceptReject->get('reject')->isClicked()) {
                    $resultado = $em->getRepository('BaseBundle:Ofertas')->updateStatus(2, $data['idO']);
                    //log de la operacion
                    $em->getRepository('BaseBundle:Log')->action2log($data['idD'], 4, $data['idC']);
                    if ($resultado) {
                        $this->get('session')->getFlashBag()->add(
                            'reject', '');
                    }
                }
            }
        }
        //recoger variables de sesión
        $session = $this->container->get('session');
        $id_partida = $session->get('id_partida');
        $session->remove('id_partida');

        return new RedirectResponse($this->get('router')->generate('partida_home', array('id_partida' => $id_partida)));
    }

    /**
     * Delete a deal
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function DeleteDealAction(Request $request)
    {
        //Get Entity Manager
        $em = $this->getDoctrine()->getManager();

        $acceptReject = $this->createForm(new DeleteDealType());
        $acceptReject->handleRequest($request);
        if ($request->isMethod('POST')) {
            $data = $acceptReject->getData();

            $oferta = $em->getRepository('BaseBundle:Ofertas')->
            checkDeal($data['idC'], $data['idP'], $data['idO'], $data['idD']);
            if (count($oferta) > 0) { //Los datos de la oferta son correctos
                if ($acceptReject->get('del')->isClicked()) {
                    $resultado = $em->getRepository('BaseBundle:Ofertas')->
                    deleteDeal($data['idC'], $data['idP'], $data['idO'], $data['idD']);

                    if ($resultado == 1) {
                        $this->get('session')->getFlashBag()->add(
                            'correctDel', '');
                    } else {
                        $this->get('session')->getFlashBag()->add(
                            'error', '');
                    }

                }
            }
        }
        //recoger variables de sesión
        $session = $this->container->get('session');
        $id_partida = $session->get('id_partida');
        $session->remove('id_partida');

        return new RedirectResponse($this->get('router')->generate('partida_home', array('id_partida' => $id_partida)));
    }

    /**
     * Controller for another user profile page
     *
     * @param Request $request
     * @param $id_partida
     * @param $username
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function jugadorAction(Request $request, $id_partida, $username)
    {
        //set language
        $locale = $request->get('_locale');
        $request->setLocale($locale);
        $request->getSession()->set('_locale', $locale);

        //get User info
        $user = $this->get('security.context')->getToken()->getUser();
        $user_id = $user->getId();

        //Get Entity Manager
        $em = $this->getDoctrine()->getManager();
        $partida = $em->getRepository('BaseBundle:Jugadores')->findOtherUserInPartidaInfo($user_id, $id_partida, $username);

        if (count($partida) > 0) {
            $playerInfo = $em->getRepository('BaseBundle:User')->profileInfo($username);
            $jugadores = $em->getRepository('BaseBundle:Jugadores')->findAllFriends($id_partida);

            //set id_partida and username in session
            $session = $this->container->get('session');
            $session->set('id_partida', $id_partida);
            $session->set('player_username', $username);
            $session->set('idCreador', $user_id);
            $session->set('idPlayer', $partida[0]['idPlayer']);
            $session->set('alu_roja_actual', $partida[0]['alu_roja_actual']);
            $session->set('alu_blanca_actual', $partida[0]['alu_blanca_actual']);

            //deal_proposal form
            $form = $this->createForm(new DealProposalType($partida[0]));
            return $this->render('BaseBundle:Partida:playerProfile.html.twig',
                array('partida' => $partida,
                    'playerInfo' => $playerInfo,
                    'jugadores' => $jugadores,
                    'form' => $form->createView(),
                    'user_id' => $user_id));

        } else {
            throw new AccessDeniedException;
        }

    }

    /**
     * Create a new deal
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function createDealAction(Request $request)
    {
        //set language
        $locale = $request->get('_locale');
        $request->setLocale($locale);
        $request->getSession()->set('_locale', $locale);

        //get session variables
        $session = $this->container->get('session');
        $id_partida = $session->get('id_partida');
        $username = $session->get('player_username');
        $idPlayer = $session->get('idPlayer');
        $idCreador = $session->get('idCreador');
        $aluRojaActual = $session->get('alu_roja_actual');
        $aluBlancaActual = $session->get('alu_blanca_actual');

        $session->remove('id_partida');
        $session->remove('player_username');
        $session->remove('idPlayer');
        $session->remove('alu_roja_actual');
        $session->remove('alu_blanca_actual');

        //si se ha alcanzado el máximo de ofertas, interrumpir operación
        $em = $this->getDoctrine()->getManager();
        $nOfertas = $em->getRepository('BaseBundle:Ofertas')->countOffers($idCreador, $id_partida);
        if ($nOfertas[0]['maxOfertas'] == 0 || $nOfertas[0]['num'] < $nOfertas[0]['maxOfertas']) {

            $partida = array('alu_roja_actual' => $aluRojaActual, 'alu_blanca_actual' => $aluBlancaActual);
            $form = $this->createForm(new DealProposalType($partida));
            $form->handleRequest($request);

            //get User info
            $user_id = $this->get('security.context')->getToken()->getUser()->getId();

            $data = $form->getData();
            if ($form->isValid() && $data['aluBlancaOut'] <= $aluBlancaActual && $data['aluRojaOut'] <= $aluRojaActual && $idPlayer != $user_id) {

                $em = $this->getDoctrine()->getManager();
                $em->getRepository('BaseBundle:Ofertas')->SetNewOffer($user_id, $idPlayer, $id_partida, $data);

                $this->get('session')->getFlashBag()->add(
                    'correct',
                    'Enviado correctamente.'
                );

                //log de la operacion: 2-> crear oferta
                $em->getRepository('BaseBundle:Log')->action2log($user_id, 2, $idPlayer);
                return new RedirectResponse($this->get('router')->generate('user_profile', array('id_partida' => $id_partida, 'username' => $username)));
            }

            $this->get('session')->getFlashBag()->add(
                'invalid_form',
                'datos inválidos.'
            );

        } else {
            $this->get('session')->getFlashBag()->add(
                'max_reached',
                'datos inválidos.');
        }
        return new RedirectResponse($this->get('router')->generate('user_profile', array('id_partida' => $id_partida, 'username' => $username)));
    }
}