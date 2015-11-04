<?php

namespace BaseBundle\Entity;

use Doctrine\ORM\EntityRepository;

class JugadoresRepository extends EntityRepository
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
                INNER JOIN BaseBundle:Jugadores jugadores
                WHERE jugadores.idJugador = :id AND CURRENT_TIMESTAMP() < partida.fin";

        $query = $entityManager->createQuery($dql);
        $query->setParameter('id', $user_id);

        return $query->getResult();
    }

    /**
     * Información de una determinada partida dado el jugador y el identificador de partida
     * @param $user_id
     * @param $id_partida
     * @return array
     */
    public function findPartidaInfo($user_id, $id_partida)
    {
        $entityManager = $this->getEntityManager();

        $dql = "SELECT partida.fin,partida.nombre,partida.id, partida.creado, partida.algUtilidad,
        jugadores.aluRojaActual, jugadores.aluBlancaActual, jugadores.fUtilidad
        FROM BaseBundle:Partida partida
        JOIN BaseBundle:Jugadores jugadores WITH partida.id = jugadores.idPartida
        WHERE jugadores.idJugador = :idJugador AND jugadores.idPartida = :idPartida";

        $query = $entityManager->createQuery($dql);
        $query->setParameter('idJugador', $user_id);
        $query->setParameter('idPartida', $id_partida);

        return $query->getResult();
    }

    /**
     * Información de una partida dado el identificador, el id de de usuario, e id de persona a la que se har� la oferta-
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

        $sql1 = "SELECT user.id FROM fos_user user WHERE user.username = :playerUsername ";
        $statement = $connection->prepare($sql1);
        $statement->bindValue('playerUsername', $player_username);
        $statement->execute();
        $id_player = $statement->fetchAll();

        $sql2 = "SELECT partida.id, partida.nombre, :idPlayer AS idPlayer , jugadores.alu_blanca_actual, jugadores.alu_roja_actual FROM partida
        INNER JOIN jugadores
        WHERE jugadores.id_jugador = :idUser AND jugadores.id_partida = :idPartida AND
        :idPlayer IN (SELECT jugadores.id_jugador FROM jugadores WHERE jugadores.id_partida = :idPartida )";

        $statement = $connection->prepare($sql2);
        $statement->bindValue('idPartida', $id_partida);
        $statement->bindValue('idUser', $user_id);
        $statement->bindValue('idPlayer', $id_player[0]['id']);

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

        $sql = "SELECT user.username, players.id_jugador FROM fos_user user INNER JOIN
        ( SELECT id_jugador FROM jugadores WHERE id_partida = :idPartida ) players
        ON user.id = players.id_jugador";

        $statement = $connection->prepare($sql);
        $statement->bindValue('idPartida', $id_partida);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function updateBeans($idJugador, $idPartida, $aluRoja, $aluBlanca)
    {

        $entityManager = $this->getEntityManager();

        $dql = "UPDATE BaseBundle:Jugadores jugadores
                SET jugadores.aluRojaActual = jugadores.aluRojaActual + :aluRoja,
                    jugadores.aluBlancaActual = jugadores.aluBlancaActual+ :aluBlanca
                WHERE jugadores.idJugador = :idJugador AND jugadores.idPartida = :idPartida";

        $query = $entityManager->createQuery($dql);
        $query->setParameter('aluRoja', $aluRoja);
        $query->setParameter('aluBlanca', $aluBlanca);
        $query->setParameter('idJugador', $idJugador);
        $query->setParameter('idPartida', $idPartida);

        return $query->getResult();
    }
}