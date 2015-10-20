<?php

namespace BaseBundle\Controller;


use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use FOS\UserBundle\Controller\SecurityController as BaseController;
use BaseBundle\Entity\Usuarios;

class DefaultController extends BaseController
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $locale = $request->get('_locale');

        $request->setLocale($locale);
        $request->getSession()->set('_locale', $locale);

        //FOS part
        $session = $request->getSession();

        $securityContext = $this->container->get('security.context');
        $router = $this->container->get('router');

        /* if ($securityContext->isGranted('ROLE_ADMIN')) {
             return new RedirectResponse($router->generate('admin_home'), 307);
         }
*/
        if ($securityContext->isGranted('ROLE_USER')) {
            //return new RedirectResponse($router->generate('user_homepage'), 307);
            return $this->redirectToRoute('user_homepage');
        }
        $authErrorKey = Security::AUTHENTICATION_ERROR;
        $lastUsernameKey = Security::LAST_USERNAME;


        // get the error if any (works with forward and redirect -- see below)
        if ($request->attributes->has($authErrorKey)) {
            $error = $request->attributes->get($authErrorKey);
        } elseif (null !== $session && $session->has($authErrorKey)) {
            $error = $session->get($authErrorKey);
            $session->remove($authErrorKey);
        } else {
            $error = null;
        }

        if (!$error instanceof AuthenticationException) {
            $error = null; // The value does not come from the security component.
        }

        // last username entered by the user
        $lastUsername = (null === $session) ? '' : $session->get($lastUsernameKey);


        $csrfToken = $this->get('security.csrf.token_manager')->getToken('authenticate')->getValue();


        return $this->render('BaseBundle:Default:index.html.twig', array(
            'last_username' => $lastUsername,
            'error' => $error,
            'csrf_token' => $csrfToken,
        ));
    }
}
