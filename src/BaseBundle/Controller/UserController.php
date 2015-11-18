<?php

namespace BaseBundle\Controller;

use BaseBundle\Form\Type\SearchGameType;
use BaseBundle\Form\Type\UpdateUserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class UserController extends Controller
{
    /**
     * user homepage controller
     *
     * @param Request $request
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function userhomeAction(Request $request)
    {
        //set language
        $locale = $request->get('_locale');
        $request->setLocale($locale);
        $request->getSession()->set('_locale', $locale);

        //Security control. Check user roles. If role is not ROLE_USER -> redirect to login
        $securityContext = $this->container->get('security.context');
        $this->checkUserRole($securityContext);

        //declaracion de variables:
        //variables de usuario
        $user = $this->get('security.context')->getToken()->getUser();
        $user_id = $user->getId();
        //variables de partida
        $now = new \DateTime('NOW');
        $misPartidasEnCurso = array();

        //llamada al Entity manager
        $em = $this->getDoctrine()->getManager();
        //búsqueda de partidas
        $partidas = $em->getRepository('BaseBundle:Jugadores')->findMisPardidas($user_id);

        foreach ($partidas as $partida) {

            $fin = $partida['fin'];
            //$partida['intervalo'] = date_diff($now, $fin);
            //pasar a milisegundos la fecha de fin (Angular usa esa variable)
            $ms = $fin->getTimestamp() * 1000;
            $partida['ms'] = $ms;

            array_push($misPartidasEnCurso, $partida);
        }

        $partidasEnCurso = $em->getRepository('BaseBundle:Partida')->findCurrentPartidas();
        return $this->render('BaseBundle:User:userhome.html.twig', array('partidas' => $misPartidasEnCurso, 'partidasEnCurso' => $partidasEnCurso));
    }


    public function profileAction(Request $request)
    {

        //set language
        $locale = $request->get('_locale');
        $request->setLocale($locale);
        $request->getSession()->set('_locale', $locale);

        //Security control. Check user roles. If role is not ROLE_USER -> redirect to login
        $securityContext = $this->container->get('security.context');
        $this->checkUserRole($securityContext);

        //user variables
        $user = $this->get('security.context')->getToken()->getUser();
        $user_id = $user->getId();

        //call Entity manager
        $em = $this->getDoctrine()->getManager();

        $userData = $em->getRepository('BaseBundle:User')->findOneById($user_id);

        $form = $this->createForm(new UpdateUserType($userData));
        $form->handleRequest($request);
        if ($request->isMethod('POST') && $form->isValid()) {
            $data = $form->getData();

            $userData->setFullName($data['fullName']);
            $userData->setEmail($data['email']);
            $em->flush();

            $this->get('session')->getFlashBag()->add('correct', '');
        }

        //Parte de log: 15 resultados
        $log = $em->getRepository('BaseBundle:Log')->getUserLog($user_id, 15);
        $logger = array();
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

        return $this->render('BaseBundle:User:profile.html.twig', array('userData' => $userData,
            'form' => $form->createView(), 'logger' => $logger));
    }

    /**
     * Checks user Role. Is User_Role is not granted -> Redirect to index homepage
     *
     * @param $securityContext
     * @return RedirectResponse
     */
    protected function checkUserRole($securityContext)
    {
        /*if (is_null($securityContext->getToken())) {
            return new RedirectResponse($router->generate('usuarios_login'), 307);
        }*/

        if (!$securityContext->isGranted('ROLE_USER')) {
            return new RedirectResponse($this->container->get('router')->generate('base_homepage'));
        }
    }
}