<?php

namespace BaseBundle\Entity;

use BaseBundle\Controller\Logic\PartidaLogic;
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
                INNER JOIN BaseBundle:Jugadores jugadores  WITH partida.id = jugadores.idPartida
                WHERE jugadores.idJugador = :id AND CURRENT_TIMESTAMP() < partida.fin";

        $query = $entityManager->createQuery($dql);
        $query->setParameter('id', $user_id);

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

    /**
     * Actualiza las alubias de un determinado jugador
     *
     * @param $idJugador
     * @param $idPartida
     * @param $aluRoja
     * @param $aluBlanca
     * @return array
     */
    public function updateBeans($idJugador, $idPartida, $aluRoja, $aluBlanca, $alg_utilidad)
    {
        $entityManager = $this->getEntityManager();
        //clase con los métodos de cáclculo de fUtilidad
        $logic = new PartidaLogic();
        $fUtilidad = $logic->calculateFUtilidad($aluRoja, $aluBlanca, $alg_utilidad);

        $dql = "UPDATE BaseBundle:Jugadores jugadores
                SET jugadores.aluRojaActual = jugadores.aluRojaActual + :aluRoja,
                    jugadores.aluBlancaActual = jugadores.aluBlancaActual+ :aluBlanca,
                    jugadores.fUtilidad = :fUtilidad
                WHERE jugadores.idJugador = :idJugador AND jugadores.idPartida = :idPartida";

        $query = $entityManager->createQuery($dql);
        $query->setParameter('aluRoja', $aluRoja);
        $query->setParameter('aluBlanca', $aluBlanca);
        $query->setParameter('idJugador', $idJugador);
        $query->setParameter('idPartida', $idPartida);
        $query->setParameter('fUtilidad', $fUtilidad);

        return $query->getResult();
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
        $logic = new PartidaLogic();

        $sql1 = "SELECT alg_utilidad, alg_reparto FROM partida WHERE partida.id = :id";
        $statement = $connection->prepare($sql1);
        $statement->bindValue('id', $id_partida);
        $statement->execute();
        $result = $statement->fetchAll();

        $algUtilidad = $result[0]['alg_utilidad'];
        $algReparto = $result[0]['alg_reparto'];
        $alubias = $logic->calculateBeans($id_partida, $algReparto);
        $fUtilidad = $logic->calculateFUtilidad($alubias['aluRojaInicial'], $alubias['aluBlancaInicial'], $algUtilidad);

        $sql2 = "INSERT INTO jugadores (id_partida, id_jugador, alu_roja_inicial, alu_blanca_inicial, alu_roja_actual,
                alu_blanca_actual, f_utilidad)
                VALUES
                (:idPartida, :idJugador,:aluRojaInicial, :aluBlancaInicial, :aluRojaActual, :aluBlancaActual, :fUtilidad)";

        $statement = $connection->prepare($sql2);

        $statement->bindValue('idPartida', $id_partida);
        $statement->bindValue('idJugador', $user_id);
        $statement->bindValue('aluRojaInicial', $alubias['aluRojaInicial']);
        $statement->bindValue('aluBlancaInicial', $alubias['aluBlancaInicial']);
        $statement->bindValue('aluRojaActual', $alubias['aluRojaInicial']);
        $statement->bindValue('aluBlancaActual', $alubias['aluBlancaInicial']);
        $statement->bindValue('fUtilidad', $fUtilidad);

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

        $dql = "SELECT user.username, user.fullName, jugadores.fUtilidad, jugadores.aluRojaActual, jugadores.aluBlancaActual
                FROM BaseBundle:Jugadores jugadores
                INNER JOIN BaseBundle:USER user WITH jugadores.idJugador = user.id
                INNER JOIN BaseBundle:Partida partida WITH jugadores.idPartida = partida.id
                WHERE jugadores.idPartida = :idPartida ORDER BY jugadores.fUtilidad DESC";

        $query = $entityManager->createQuery($dql);
        $query->setParameter('idPartida', $id_partida);

        return $query->getResult();
    }
}