<?php

namespace BaseBundle\Tests\Forms;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class TypeTestCase
 */
abstract class Type extends KernelTestCase
{
    protected $formFactory;
    protected $em = null;

    protected function setUp()
    {
        static::bootKernel();
        $this->formFactory = static::$kernel->getContainer()->get('form.factory');
        $this->em = static::$kernel->getContainer()->get('doctrine')->getManager();
    }
}