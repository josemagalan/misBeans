<?php

namespace BaseBundle\EventListener;

use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Listener for login action LastLoginListener
 *
 * Class LoginSuccessListener
 * @package BaseBundle\EventListener
 */
class LoginSuccessListener implements EventSubscriberInterface
{
    private $router;

    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::SECURITY_IMPLICIT_LOGIN => array('onLoginSuccess', -10),
        );
    }

    public function onLoginSuccess(FormEvent $event)
    {

        $url = $this->router->generate('user_homepage');

        $event->setResponse(new RedirectResponse($url));
    }
}