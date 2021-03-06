<?php

namespace BaseBundle\Controller;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use FOS\UserBundle\Controller\SecurityController as BaseController;
use BaseBundle\Entity\Usuarios;

/**
 * Class DefaultController
 * @package BaseBundle\Controller
 */
class DefaultController extends BaseController
{
    /**
     * Index of the app
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $locale = $request->get('_locale');
        $request->setLocale($locale);
        $request->getSession()->set('_locale', $locale);
        $session = $request->getSession();
        $securityContext = $this->container->get('security.context');

        if ($securityContext->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('admin_homepage');
        } elseif ($securityContext->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('user_homepage');
        }
        else {
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
}
