<?php

namespace BaseBundle\Entity;

use Doctrine\ORM\EntityRepository;

class PartidaRepository extends EntityRepository
{

    /**
     * Busca las partidas creadas por un determinado administrador
     *
     * @param $admin_id
     * @return array
     */
    public function findAdminPardidas($admin_id)
    {
        $entityManager = $this->getEntityManager();

        $dql = "SELECT partida.nombre, partida.fin,partida.creado,partida.id FROM BaseBundle:Partida partida
        WHERE partida.idCreador = :id";

        $query = $entityManager->createQuery($dql);
        $query->setParameter('id', $admin_id);

        return $query->getResult();
    }

    /**
     * Selecciona todas aquellas partidas que están en juego
     *
     * @return array
     */
    public function findCurrentPartidas()
    {
        $entityManager = $this->getEntityManager();

        $dql = "SELECT partida.nombre, partida.fin,partida.creado,partida.id FROM BaseBundle:Partida partida
        WHERE CURRENT_TIMESTAMP() < partida.fin";

        $query = $entityManager->createQuery($dql);

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
     * Guarda una nueva partida
     *
     * @param $data
     * @param $admin_id
     * @return bool
     * @throws \Doctrine\DBAL\DBALException
     */
    public function SetNewPartida($data, $admin_id)
    {
        $entityManager = $this->getEntityManager();
        $connection = $entityManager->getConnection();

        $dql = "INSERT INTO partida (nombre, password, creado, id_creador, fin, max_jugadores, max_ofertas, tiempo_oferta,
                alg_utilidad, alg_reparto, alu_roja, alu_blanca)
                VALUES (:nombre,:password, now(), :idCreador, :fin, :maxJugadores, :maxOfertas, :tiempoOferta, :algUtilidad,
                :algReparto, :aluRoja, :aluBlanca)";

        $statement = $connection->prepare($dql);

        $statement->bindValue('nombre', $data['nombre']);
        $statement->bindValue('password', $data['password']);
        $statement->bindValue('idCreador', intval($admin_id));
        $statement->bindValue('fin', $data['fin']->format('Y-m-d H:i:s'));
        $statement->bindValue('maxJugadores', $data['maxJugadores']);
        $statement->bindValue('maxOfertas', $data['maxOfertas']);
        $statement->bindValue('tiempoOferta', $data['tiempoOferta']);
        $statement->bindValue('algUtilidad', $data['algUtilidad']);
        $statement->bindValue('algReparto', $data['algReparto']);
        $statement->bindValue('aluRoja', $data['aluRoja']);
        $statement->bindValue('aluBlanca', $data['aluBlanca']);

        return $statement->execute();
    }

    /**
     * Comprueba una partida dado un identificador y un creador. Retorna los datos de la misma.
     *
     * @param $idPartida
     * @param $idAdmin
     *
     * @return array
     */
    public function isMyAdminGame($idPartida, $idAdmin)
    {
        $entityManager = $this->getEntityManager();

        $dql = "SELECT partida.nombre, partida.fin,partida.creado,partida.id, partida.algUtilidad
        FROM BaseBundle:Partida partida
        WHERE partida.id = :id AND partida.idCreador = :idCreador";

        $query = $entityManager->createQuery($dql);
        $query->setParameter('id', $idPartida);
        $query->setParameter('idCreador', $idAdmin);

        return $query->getResult();
    }
}