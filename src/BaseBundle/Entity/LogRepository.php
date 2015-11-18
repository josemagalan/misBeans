<?php

namespace BaseBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Log:
 *      1: ingresar en una partida
 *      2: nueva oferta
 *      3: aceptar_oferta
 *      4: rechazar_ ferta
 *      5: nueva partida
 *
 * Class LogRepository
 * @package BaseBundle\Entity
 */
class LogRepository extends EntityRepository
{

    public function action2log($userId, $actionId, $actionData)
    {

        $entityManager = $this->getEntityManager();
        $connection = $entityManager->getConnection();

        $sql = "INSERT INTO log (user,fecha,actionId, actionData)
                VALUES
                (:userId, now(), :actionId, :actionData)";

        $statement = $connection->prepare($sql);

        $statement->bindValue('userId', $userId);
        $statement->bindValue('actionId', $actionId);
        $statement->bindValue('actionData', $actionData);

        return $statement->execute();
    }

    public function getUserLog($userId, $limit)
    {
        $entityManager = $this->getEntityManager();

        $dql = "SELECT IDENTITY(log.user) AS user, log.fecha, log.actionId, log.actionData
        FROM BaseBundle:Log log
        WHERE log.user= :userId ORDER BY log.fecha DESC";

        $query = $entityManager->createQuery($dql);
        $query->setParameter('userId', $userId);
        $query->setMaxResults($limit);

        return $query->getResult();

    }
}