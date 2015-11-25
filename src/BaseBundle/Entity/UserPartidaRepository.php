<?php

namespace BaseBundle\Entity;

use BaseBundle\Controller\Logic\PartidaLogic;
use Doctrine\ORM\EntityRepository;

class UserPartidaRepository extends EntityRepository
{

    /**
     * Partidas en las que está registrado un usuario.
     *
     * @param $user_id
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
     * Información de una partida dado el identificador, el id de de usuario, e id de persona a la que se hace la oferta
     * @param $user_id
     * @param $id_partida
     * @param $player_username
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function findOtherUserInPartidaInfo($user_id, $id_partida, $player_username)
    {
        $entityManager = $this->getEntityManager();
        $connection = $entityManager->getConnection();

        $sql1 = "SELECT user.id , user.email FROM user user WHERE user.username = :playerUsername";
        $statement = $connection->prepare($sql1);
        $statement->bindValue('playerUsername', $player_username);
        $statement->execute();
        $player = $statement->fetchAll();

        $sql2 = "SELECT partida.id, partida.nombre, :idPlayer AS idPlayer, :playerEmail AS playerEmail,
        userpartida.alu_blanca_actual, userpartida.alu_roja_actual
        FROM partida
        INNER JOIN userpartida ON partida.id = userpartida.id_partida
        WHERE userpartida.id_user = :idUser AND userpartida.id_partida = :idPartida AND
        :idPlayer IN (SELECT userpartida.id_user FROM userpartida WHERE userpartida.id_partida = :idPartida )";

        $statement = $connection->prepare($sql2);
        $statement->bindValue('idPartida', $id_partida);
        $statement->bindValue('idUser', $user_id);
        $statement->bindValue('idPlayer', $player[0]['id']);
        $statement->bindValue('playerEmail', $player[0]['email']);

        $statement->execute();

        return $statement->fetchAll();

    }

    /**
     * Todos los jugadores que se encuentran en una partida.
     *
     * @param $id_partida
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
     * Actualiza las alubias de un determinado jugador
     *
     * @param $idUser
     * @param $idPartida
     * @param $aluRoja
     * @param $aluBlanca
     * @return array
     */
    public function updateBeans($idUser, $idPartida, $aluRoja, $aluBlanca)
    {
        $entityManager = $this->getEntityManager();

        //seleccionar la partida sobre la que se va a trabajar para obtener el objeto y las alubias
        $dql = "SELECT up FROM  BaseBundle:UserPartida up
                WHERE up.idUser = :idUser AND up.idPartida = :idPartida";
        $query = $entityManager->createQuery($dql);
        $query->setParameter('idUser', $idUser);
        $query->setParameter('idPartida', $idPartida);

        $userPartida = $query->getSingleResult();

        //el método de cálculo de la utilidad necesita el objeto partida para extraer datos
        $partida = $entityManager->getRepository('BaseBundle:Partida')->findOneById($idPartida);
        //clase con los métodos de cáclculo de fUtilidad
        $logic = new PartidaLogic();
        $fUtilidad = $logic->calculateFUtilidad($userPartida->getAluRojaActual() + $aluRoja,
            $userPartida->getAluBlancaActual() + $aluBlanca, $partida);

        //añadir a las ya existentes las nuevas
        $userPartida->setAluRojaActual($userPartida->getAluRojaActual() + $aluRoja);
        $userPartida->setAluBlancaActual($userPartida->getAluBlancaActual() + $aluBlanca);
        $userPartida->setFUtilidad($fUtilidad);

        return $entityManager->flush($userPartida);
    }

    /**
     * @param $user_id
     * @param $id_partida
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
     * @param $id_partida
     * @return array
     */
    public function getRanking($id_partida)
    {
        $entityManager = $this->getEntityManager();

        $dql = "SELECT user.username, user.fullName, userpartida.fUtilidad, userpartida.aluRojaActual, userpartida.aluBlancaActual
                FROM BaseBundle:UserPartida userpartida
                INNER JOIN BaseBundle:User user WITH userpartida.idUser = user.id
                INNER JOIN BaseBundle:Partida partida WITH userpartida.idPartida = partida.id
                WHERE userpartida.idPartida = :idPartida ORDER BY userpartida.fUtilidad DESC";

        $query = $entityManager->createQuery($dql);
        $query->setParameter('idPartida', $id_partida);

        return $query->getResult();
    }

    /**
     * Actualiza el estado de usuario en la partida ocn nuevos datos
     *
     * @param $idUser
     * @param $idPartida
     * @param $aluRoja
     * @param $aluBlanca
     * @param $fUtilidad
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