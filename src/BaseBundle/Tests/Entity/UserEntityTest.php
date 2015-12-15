<?php

namespace BaseBundle\Tests\Entity;


class UserEntityTest extends Entity
{

    public function testUser()
    {
        $users = $this->em
            ->getRepository('BaseBundle:User')
            ->findOneByUsername('sergyzen');

        $this->assertEquals(!null,$users != null);
    }

}
