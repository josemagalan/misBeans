<?php

namespace BaseBundle\Controller;

use BaseBundle\Controller\Logic\Loglogic;
use BaseBundle\Form\Type\SearchGameType;
use BaseBundle\Form\Type\UpdateUserType;
use BaseBundle\Controller\Logic\Gravatar;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;

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
        $misPartidasEnCurso = array();

        //imagen Gravatar
        $gravatar = $this->getGravatar($user->getEmail());

        //llamada al Entity manager
        $em = $this->getDoctrine()->getManager();
        //búsqueda de partidas
        $partidas = $em->getRepository('BaseBundle:UserPartida')->findMisPardidas($user_id);

        foreach ($partidas as $partida) {

            $fin = $partida['fin'];
            //$partida['intervalo'] = date_diff($now, $fin);
            //pasar a milisegundos la fecha de fin (Angular usa esa variable)
            $ms = $fin->getTimestamp() * 1000;
            $partida['ms'] = $ms;

            array_push($misPartidasEnCurso, $partida);
        }

        $partidasEnCurso = $em->getRepository('BaseBundle:Partida')->findCurrentPartidas();
        return $this->render('BaseBundle:User:userhome.html.twig',
            array('partidas' => $misPartidasEnCurso,
                'partidasEnCurso' => $partidasEnCurso,
                'gravatar' => $gravatar
            ));
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
        $user = $this->getUser();
        $user_id = $user->getId();

        //imagen Gravatar
        $gravatar = $this->getGravatar($user->getEmail());

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

        //buscar log de usuario
        $logic = new Loglogic();
        $logger = $logic->getLog($user_id, $locale, $em);

        //actualizar password
        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->get('fos_user.change_password.form.factory');

        $formPassword = $formFactory->createForm();
        $formPassword->setData($user);

        return $this->render('BaseBundle:User:profile.html.twig',
            array('userData' => $userData,
                'form' => $form->createView(),
                'logger' => $logger,
                'gravatar' => $gravatar,
                'formPassword' => $formPassword->createView(),
            ));
    }

    /**
     * Change password action. Redirects to user profile
     *
     * @param Request $request
     * @return null|RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function changePasswordAction(Request $request)
    {
        $user = $this->getUser();

        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::CHANGE_PASSWORD_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->get('fos_user.change_password.form.factory');

        $form = $formFactory->createForm();
        $form->setData($user);
        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
            $userManager = $this->get('fos_user.user_manager');

            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::CHANGE_PASSWORD_SUCCESS, $event);

            $userManager->updateUser($user);

            if (null === $response = $event->getResponse()) {
                $url = $this->generateUrl('user_profile_my');
                $response = new RedirectResponse($url);
            }

            $dispatcher->dispatch(FOSUserEvents::CHANGE_PASSWORD_COMPLETED, new FilterUserResponseEvent($user, $request, $response));
            $this->get('session')->getFlashBag()->add('correct', '');

            return $response;
        }

        $this->get('session')->getFlashBag()->add('error', '');
        return new RedirectResponse($this->container->get('router')->generate('user_profile_my'));

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

    /**
     * Gets the gravatar URL for an email
     *
     * @param $email
     * @return String
     */
    protected function getGravatar($email)
    {
        $grav = new Gravatar($email);
        $gravatar = $grav->get_gravatar();
        return $gravatar;
    }
}