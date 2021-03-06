<?php

namespace BaseBundle\Entity;

use Doctrine\ORM\EntityRepository;

class UserPartidaRepository extends EntityRepository
{

    /**
     * Partidas en las que está registrado un usuario.
     *
     * @param int $user_id
     * @return array
     */
    public function findMisPardidas($user_id)
    {
        $entityManager = $this->getEntityManager();

        $dql = "SELECT partida.nombre, partida.fin,partida.creado,partida.id
                FROM BaseBundle:Partida partida
                INNER JOIN BaseBundle:UserPartida userpartida  WITH partida.id = userpartida.idPartida
                WHERE userpartida.idUser = :idUser AND CURRENT_TIMESTAMP() < partida.fin";

        $query = $entityManager->createQuery($dql);
        $query->setParameter('idUser', $user_id);

        return $query->getResult();
    }

    /**
     * Todos los jugadores que se encuentran en una partida.
     *
     * @param int $id_partida
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function findAllFriends($id_partida)
    {
        $entityManager = $this->getEntityManager();
        $connection = $entityManager->getConnection();

        $sql = "SELECT user.username, players.id_user FROM user user INNER JOIN
        ( SELECT id_user FROM userpartida WHERE id_partida = :idPartida ) players
        ON user.id = players.id_user";

        $statement = $connection->prepare($sql);
        $statement->bindValue('idPartida', $id_partida);
        $statement->execute();

        return $statement->fetchAll();
    }

    /**
     * @param int $idUser
     * @param int $idPartida
     * @return UserPartida
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByIDS($idUser, $idPartida)
    {
        $entityManager = $this->getEntityManager();

        //seleccionar la partida sobre la que se va a trabajar para obtener el objeto y las alubias
        $dql = "SELECT up FROM  BaseBundle:UserPartida up
                WHERE up.idUser = :idUser AND up.idPartida = :idPartida";
        $query = $entityManager->createQuery($dql);
        $query->setParameter('idUser', $idUser);
        $query->setParameter('idPartida', $idPartida);

        return $query->getSingleResult();
    }

    /**
     * @param int $user_id
     * @param int $id_partida
     * @return bool
     * @throws \Doctrine\DBAL\DBALException
     */
    public function addJugador($user_id, $id_partida)
    {
        $entityManager = $this->getEntityManager();
        $connection = $entityManager->getConnection();

        $sql = "INSERT INTO userpartida (id_partida, id_user)
                VALUES (:idPartida, :idUser)";

        $statement = $connection->prepare($sql);

        $statement->bindValue('idPartida', $id_partida);
        $statement->bindValue('idUser', $user_id);

        return $statement->execute();
    }

    /**
     * Consulta el ranking de la partida indicada
     *
     * @param int $id_partida
     * @return array
     */
    public function getRanking($id_partida)
    {
        $entityManager = $this->getEntityManager();

        $dql = "SELECT user.username, user.fullName, userpartida.fUtilidad, userpartida.aluRojaActual, userpartida.aluBlancaActual
                FROM BaseBundle:UserPartida userpartida
                INNER JOIN BaseBundle:user user WITH userpartida.idUser = user.id
                INNER JOIN BaseBundle:Partida partida WITH userpartida.idPartida = partida.id
                WHERE userpartida.idPartida = :idPartida ORDER BY userpartida.fUtilidad DESC";

        $query = $entityManager->createQuery($dql);
        $query->setParameter('idPartida', $id_partida);

        return $query->getResult();
    }

    /**
     * Actualiza el estado de usuario en la partida com nuevos datos
     *
     * @param int $idUser
     * @param int $idPartida
     * @param int $aluRoja
     * @param int $aluBlanca
     * @param int $fUtilidad
     * @return array
     */
    public function distributeBeans($idUser, $idPartida, $aluRoja, $aluBlanca, $fUtilidad)
    {
        $entityManager = $this->getEntityManager();

        $dql = "UPDATE BaseBundle:UserPartida userpartida
                SET userpartida.aluRojaActual = :aluRoja,
                    userpartida.aluBlancaActual = :aluBlanca,
                    userpartida.aluBlancaInicial = :aluBlanca,
                    userpartida.aluRojaInicial = :aluRoja,
                    userpartida.fUtilidad = :fUtilidad
                WHERE userpartida.idUser = :idUser AND userpartida.idPartida = :idPartida";

        $query = $entityManager->createQuery($dql);
        $query->setParameter('aluRoja', $aluRoja);
        $query->setParameter('aluBlanca', $aluBlanca);
        $query->setParameter('idUser', $idUser);
        $query->setParameter('idPartida', $idPartida);
        $query->setParameter('fUtilidad', $fUtilidad);

        return $query->getResult();
    }
}