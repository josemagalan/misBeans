<?php

namespace BaseBundle\Entity;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    public function profileInfo($username)
    {
        $entityManager = $this->getEntityManager();
        $dql = "SELECT user.username, user.fullName, user.email
        FROM BaseBundle:User user WHERE user.username = :nombre";

        $query = $entityManager->createQuery($dql);
        $query->setParameter('nombre', $username);

        return $query->getResult();
    }

}