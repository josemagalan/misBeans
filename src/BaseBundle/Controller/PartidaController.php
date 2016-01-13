<?php

namespace BaseBundle\Controller;

use BaseBundle\Controller\Logic\GraphicsLogic;
use BaseBundle\Controller\Logic\Gravatar;
use BaseBundle\Controller\Logic\Loglogic;
use BaseBundle\Controller\Logic\OfertaLogic;
use BaseBundle\Controller\Logic\PartidaLogic;
use BaseBundle\Controller\Logic\UserPartidaLogic;
use BaseBundle\Entity\Ofertas;
use BaseBundle\Entity\Partida;
use BaseBundle\Entity\User;
use BaseBundle\Entity\UserPartida;
use BaseBundle\Form\Type\AcceptRejectDealType;
use BaseBundle\Form\Type\DealProposalType;
use BaseBundle\Form\Type\DeleteDealType;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class PartidaController
 * @package BaseBundle\Controller
 */
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

        $user = $this->getUser();
        $user_id = $user->getId();
        $gravatar = $this->getGravatar($user->getEmail());
        /** @var ObjectManager $em */
        $em = $this->getDoctrine()->getManager();
        $router = $this->container->get('router');

        try {
            $em->getRepository('BaseBundle:UserPartida')->findByIDS($user_id, $id_partida);
            return new RedirectResponse($this->get('router')->generate('partida_home', array('id_partida' => $id_partida)));
        } catch (\Exception $e) {
            /** @var Partida $partida */
            $partida = $em->getRepository('BaseBundle:Partida')->findOneById($id_partida);
            if ($partida->getEmpezado()) {
                $this->get('session')->getFlashBag()->add('started', '');
            } else {
                $partidaLogic = new PartidaLogic();

                if ($partida->getPassword() == null) {
                    $partidaLogic->newPlayer($partida, $user_id, $em);
                    return new RedirectResponse($this->container->get('router')->
                    generate('partida_home', array('id_partida' => $id_partida)));
                } else {
                    //creamos el formulario
                    $form = $this->createFormBuilder()
                        ->add('password', 'password', array('required' => true,
                            'label' => 'partida.password', 'translation_domain' => 'BaseBundle'))
                        ->getForm();

                    $form->handleRequest($request);
                    if ($form->isValid() && $request->isMethod('POST')) {
                        $data = $form->getData();
                        if (strcmp($data['password'], $partida->getPassword()) == 0) {
                            $partidaLogic->newPlayer($partida, $user_id, $em);
                            return new RedirectResponse($this->container->get('router')->
                            generate('partida_home', array('id_partida' => $id_partida)));
                        } else {
                            $form->get('password')->addError(new FormError($this->get('translator')->trans('Incorrect password')));
                        }
                    }
                    return $this->render('BaseBundle:Partida:registerP.html.twig',
                        array('partida' => $partida,
                            'form' => $form->createView(),
                            'gravatar' => $gravatar,
                        ));
                }
            }
            return new RedirectResponse($router->generate('base_homepage'));
        }
    }


    /**
     * Controller of games main page
     *
     * @param Request $request
     * @param int $id_partida
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function partidaAction(Request $request, $id_partida)
    {
        //$this->denyAccessUnlessGranted('ROLE_USER', null, 'Unable to access this page!');
        //Security control. Check user roles.
        $securityContext = $this->get('security.authorization_checker');
        if (false === $securityContext->isGranted('ROLE_USER')) {
            return new RedirectResponse($this->container->get('router')->generate('base_homepage'));
        }

        //set language
        $locale = $request->get('_locale');
        $request->setLocale($locale);
        $request->getSession()->set('_locale', $locale);

        $router = $this->container->get('router');
        /** @var User $user */
        $user = $this->getUser();
        $user_id = $user->getId();
        $gravatar = $this->getGravatar($user->getEmail());
        /** @var ObjectManager $em */
        $em = $this->getDoctrine()->getManager();

        try {
            /** @var Partida $partida */
            $partida = $em->getRepository('BaseBundle:Partida')->findOneById($id_partida);
            /** @var UserPartida $userPartida */
            $userPartida = $em->getRepository('BaseBundle:UserPartida')->findByIDS($user_id, $id_partida);

            if (new \DateTime('NOW') < $partida->getFin()) {
                $partidaLogic = new PartidaLogic();

                //consultar el resto de jugadores
                $jugadores = $em->getRepository('BaseBundle:UserPartida')->findAllFriends($id_partida);

                //consultar ofertas pendientes
                $oferta_recibida = $em->getRepository('BaseBundle:Ofertas')->findRecievedOffers($user_id, $id_partida, OfertaLogic::NOTRATADA);
                $oferta_recibida_enCurso = $partidaLogic->checkInProgress($oferta_recibida, $em);

                //consultar ofertas enviadas en curso.
                $oferta_enviada = $em->getRepository('BaseBundle:Ofertas')->findSentOffers($user_id, $id_partida, OfertaLogic::NOTRATADA);
                $oferta_enviada_enCurso = $partidaLogic->checkInProgress($oferta_enviada, $em);

                $acceptReject = $this->createForm(new AcceptRejectDealType());
                $delForm = $this->createForm(new DeleteDealType());

                //guardar en sesion el valor de partida
                $session = $this->container->get('session');
                $session->set('id_partida', $id_partida);

                /* Grafica de linea para usuario */
                $graphics = new GraphicsLogic();
                $evolucion = $partidaLogic->partidaEvolution($em, $user_id, $partida);
                $lineChart = $graphics->linesUserJsArray($evolucion);

                return $this->render('BaseBundle:Partida:partida.html.twig',
                    array('partida' => $partida,
                        'userPartida' => $userPartida,
                        'jugadores' => $jugadores,
                        'oferta_recibida' => $oferta_recibida_enCurso,
                        'oferta_enviada' => $oferta_enviada_enCurso,
                        'acceptReject' => $acceptReject->createView(),
                        'delForm' => $delForm->createView(),
                        'lineChart' => $lineChart,
                        'gravatar' => $gravatar,
                    ));
            }
        } catch (\Exception $e) {
            return new RedirectResponse($router->generate('base_homepage'));
        }
    }

    /**
     * Accept or reject a deal
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function acceptRejectAction(Request $request)
    {
        /** @var ObjectManager $em */
        $em = $this->getDoctrine()->getManager();

        $acceptReject = $this->createForm(new AcceptRejectDealType());
        $acceptReject->handleRequest($request);
        if ($request->isMethod('POST')) {
            $data = $acceptReject->getData();

            /*  idC -> idCreador
                idD -> idDestinatario
                idP -> idPartida
                idO -> idOferta
            */
            try {
                $em->getRepository('BaseBundle:Ofertas')->checkDeal($data['idC'], $data['idP'], $data['idO'], $data['idD']);
                if ($acceptReject->get('accept')->isClicked()) {

                    /** @var Ofertas $oferta */
                    $oferta = $em->getRepository('BaseBundle:Ofertas')->findOneById($data['idO']);
                    $userPartidaLogic = new UserPartidaLogic();

                    $aluBlanca = $oferta->getAluBlancaIn() - $oferta->getAluBlancaOut();
                    $aluRoja = $oferta->getAlurojaIn() - $oferta->getAluRojaOut();
                    $resultado1 = $userPartidaLogic->updateBeans($data['idC'], $data['idP'], $aluRoja, $aluBlanca, $em);

                    $aluBlanca = $oferta->getAluBlancaOut() - $oferta->getAluBlancaIn();
                    $aluRoja = $oferta->getAluRojaOut() - $oferta->getAlurojaIn();
                    $resultado2 = $userPartidaLogic->updateBeans($data['idD'], $data['idP'], $aluRoja, $aluBlanca, $em);

                    $resultado3 = $em->getRepository('BaseBundle:Ofertas')->updateStatus(OfertaLogic::ACEPTADA, $data['idO']);
                    $em->getRepository('BaseBundle:Log')->action2log($data['idD'], Loglogic::ACEPTAROFERTA, $data['idC']);
                    if ($resultado1 && $resultado2 && $resultado3) {
                        $this->get('session')->getFlashBag()->add('correct', '');
                    } else {
                        $this->get('session')->getFlashBag()->add('error', '');
                    }
                } elseif ($acceptReject->get('reject')->isClicked()) {
                    $resultado = $em->getRepository('BaseBundle:Ofertas')->updateStatus(OfertaLogic::RECHAZADA, $data['idO']);
                    $em->getRepository('BaseBundle:Log')->action2log($data['idD'], Loglogic::RECHAZAROFERTA, $data['idC']);
                    if ($resultado) {
                        $this->get('session')->getFlashBag()->add('reject', '');
                    }
                }
            } catch (\Exception $e) {
                $this->get('session')->getFlashBag()->add('error', '');
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
    public function deleteDealAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $acceptReject = $this->createForm(new DeleteDealType());
        $acceptReject->handleRequest($request);

        try {
            if ($request->isMethod('POST')) {
                $data = $acceptReject->getData();
                $em->getRepository('BaseBundle:Ofertas')->checkDeal($data['idC'], $data['idP'], $data['idO'], $data['idD']);
                if ($acceptReject->get('del')->isClicked()) {
                    $resultado = $em->getRepository('BaseBundle:Ofertas')->
                    deleteDeal($data['idC'], $data['idP'], $data['idO'], $data['idD']);
                    if ($resultado) {
                        $this->get('session')->getFlashBag()->add('correctDel', '');
                    } else {
                        $this->get('session')->getFlashBag()->add('error', '');
                    }
                }
            }
        } catch (\Exception $e) {
            $this->get('session')->getFlashBag()->add('error', '');
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
     * @param int $id_partida
     * @param String $username
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function jugadorAction(Request $request, $id_partida, $username)
    {
        //set language
        $locale = $request->get('_locale');
        $request->setLocale($locale);
        $request->getSession()->set('_locale', $locale);

        /** @var User $user */
        $user = $this->getUser();
        $gravatar = $this->getGravatar($user->getEmail());
        $em = $this->getDoctrine()->getManager();

        try {
            /** @var User $playerInfo */
            $playerInfo = $em->getRepository('BaseBundle:User')->findOneByUsername($username);
            /** @var UserPartida $userPartida */
            $userPartida = $em->getRepository('BaseBundle:UserPartida')->findByIDS($user->getId(), $id_partida);
            /** @var Partida $partida */
            $partida = $em->getRepository('BaseBundle:Partida')->findOneById($id_partida);
            $jugadores = $em->getRepository('BaseBundle:UserPartida')->findAllFriends($id_partida);

            //get Other user Gravatar
            $playerGravatar = $this->getGravatar($playerInfo->getEmail());

            $session = $this->container->get('session');
            $session->set('id_partida', $id_partida);
            $session->set('player_username', $username);
            $session->set('idPlayer', $playerInfo->getId());

            $form = $this->createForm(new DealProposalType($userPartida));
            return $this->render('BaseBundle:Partida:playerProfile.html.twig',
                array('partida' => $partida,
                    'userPartida' => $userPartida,
                    'playerInfo' => $playerInfo,
                    'jugadores' => $jugadores,
                    'form' => $form->createView(),
                    'gravatar' => $gravatar,
                    'playerGravatar' => $playerGravatar,
                ));
        } catch (\Exception $e) {
            return new RedirectResponse($this->get('router')->generate('partida_home', array('id_partida' => $id_partida)));
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

        $user_id = $this->getUser()->getId();

        $session->remove('id_partida');
        $session->remove('player_username');
        $session->remove('idPlayer');

        //si se ha alcanzado el máximo de ofertas, interrumpir operación
        $em = $this->getDoctrine()->getManager();
        $nOfertas = count($em->getRepository('BaseBundle:Ofertas')->currentOffers($user_id, $id_partida));
        /** @var Partida $partida */
        $partida = $em->getRepository('BaseBundle:Partida')->findOneById($id_partida);
        if ($partida->getMaxOfertas() == PartidaLogic::OFERTASSINLIMITE || $nOfertas < $partida->getMaxOfertas()) {

            /** @var UserPartida $userPartida */
            $userPartida = $em->getRepository('BaseBundle:UserPartida')->findByIDS($user_id, $id_partida);
            $form = $this->createForm(new DealProposalType($userPartida));
            $form->handleRequest($request);;
            $data = $form->getData();
            if ($form->isValid() && $data['aluBlancaOut'] <= $userPartida->getAluBlancaActual()
                && $data['aluRojaOut'] <= $userPartida->getAluRojaActual() && $idPlayer != $user_id
            ) {
                //los 4 valores no pueden ser iguales:
                if ($data['aluBlancaOut'] == $data['aluRojaOut'] && $data['aluBlancaIn'] == $data['aluRojaIn'] &&
                    $data['aluBlancaOut'] == $data['aluBlancaIn']
                ) {
                    $this->get('session')->getFlashBag()->add('4values', '');
                } elseif (!$partida->getEmpezado()) {
                    $this->get('session')->getFlashBag()->add('notStarted', '');
                } else {
                    $em->getRepository('BaseBundle:Ofertas')->SetNewOffer($user_id, $idPlayer, $id_partida, $data);
                    $this->get('session')->getFlashBag()->add('correct', '');
                    $em->getRepository('BaseBundle:Log')->action2log($user_id, Loglogic::NUEVAOFERTA, $idPlayer);
                }
            } else {
                $this->get('session')->getFlashBag()->add('invalid_form', '');
            }
        } else {
            $this->get('session')->getFlashBag()->add('max_reached', '');
        }
        return new RedirectResponse($this->get('router')->generate('user_profile',
            array('id_partida' => $id_partida, 'username' => $username)));
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