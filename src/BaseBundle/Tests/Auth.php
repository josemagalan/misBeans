<?php

namespace BaseBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;

/**
 * Class Auth
 * @package BaseBundle\Tests
 */
class Auth extends WebTestCase
{
    protected $client = null;
    protected $formFactory = null;
    protected $securityContext = null;
    protected $em = null;

    public function setUp()
    {
        $this->client = $this->createAuthorizedClient();
        static::bootKernel();
        $this->formFactory = static::$kernel->getContainer()->get('form.factory');
        $this->securityContext = self::$kernel->getContainer()->get('security.context');
        $this->em = static::$kernel->getContainer()->get('doctrine')->getManager();

    }

    protected function createAuthorizedClient()
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $session = $container->get('session');

        /** @var $userManager \FOS\UserBundle\Doctrine\UserManager */
        $userManager = $container->get('fos_user.user_manager');
        /** @var $loginManager \FOS\UserBundle\Security\LoginManager */
        $loginManager = $container->get('fos_user.security.login_manager');
        $firewallName = $container->getParameter('fos_user.firewall_name');

        $user = $userManager->findUserBy(array('username' => 'petete'));
        $loginManager->loginUser($firewallName, $user);

        // save the login token into the session and put it in a cookie
        $container->get('session')->set('_security_' . $firewallName,
            serialize($container->get('security.context')->getToken()));
        $container->get('session')->save();
        $client->getCookieJar()->set(new Cookie($session->getName(), $session->getId()));

        return $client;
    }


    protected function createAuthorizedAdmin()
    {
        $admin = static::createClient();
        $container = $admin->getContainer();
        $session = $container->get('session');

        /** @var $userManager \FOS\UserBundle\Doctrine\UserManager */
        $userManager = $container->get('fos_user.user_manager');
        /** @var $loginManager \FOS\UserBundle\Security\LoginManager */
        $loginManager = $container->get('fos_user.security.login_manager');
        $firewallName = $container->getParameter('fos_user.firewall_name');

        $user = $userManager->findUserBy(array('username' => 'sergyzen'));
        $loginManager->loginUser($firewallName, $user);

        // save the login token into the session and put it in a cookie
        $container->get('session')->set('_security_' . $firewallName,
            serialize($container->get('security.context')->getToken()));
        $container->get('session')->save();
        $admin->getCookieJar()->set(new Cookie($session->getName(), $session->getId()));

        return $admin;
    }

}