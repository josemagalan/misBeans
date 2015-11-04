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
        $partidasEnCurso = array();

        //llamada al Entity manager
        $em = $this->getDoctrine()->getManager();
        //búsqueda de partidas
        $partidas = $em->getRepository('BaseBundle:Jugadores')->findMisPardidas($user_id);

        foreach ($partidas as $partida) {

            $fin = $partida['fin'];
            $partida['intervalo'] = date_diff($now, $fin);
            array_push($partidasEnCurso, $partida);
        }

        $form = $this->createForm(new SearchGameType());
        $form->handleRequest($request);
        if ($request->isMethod('POST')) {
            $data = $form->getData();
            if ($form->isValid()) {
                //TODO
            }
        }
        return $this->render('BaseBundle:User:userhome.html.twig', array('form' => $form->createView(), 'partidas' => $partidasEnCurso));
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

        return $this->render('BaseBundle:User:profile.html.twig', array('userData' => $userData, 'form' => $form->createView()));
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