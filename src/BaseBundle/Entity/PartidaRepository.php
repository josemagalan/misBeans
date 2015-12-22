<?php

namespace BaseBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Class PartidaRepository
 * @package BaseBundle\Entity
 */
class PartidaRepository extends EntityRepository
{

    /**
     * Busca las partidas creadas por un determinado administrador
     *
     * @param int $admin_id
     * @return array
     */
    public function findAdminPardidas($admin_id)
    {
        $entityManager = $this->getEntityManager();

        $dql = "SELECT partida FROM BaseBundle:Partida partida
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

        $dql = "SELECT partida FROM BaseBundle:Partida partida
        WHERE CURRENT_TIMESTAMP() < partida.fin";

        $query = $entityManager->createQuery($dql);

        return $query->getResult();
    }

    /**
     * Guarda una nueva partida
     *
     * @param array $data
     * @param int $admin_id
     * @return bool
     * @throws \Doctrine\DBAL\DBALException
     */
    public function SetNewPartida($data, $admin_id)
    {
        $entityManager = $this->getEntityManager();
        $connection = $entityManager->getConnection();

        $dql = "INSERT INTO partida (nombre, password, creado, id_creador, fin, max_jugadores, max_ofertas, tiempo_oferta,
                ratio,  alu_por_usuario, exp_y, exp_z)
                VALUES (:nombre,:password, now(), :idCreador, :fin, :maxJugadores, :maxOfertas, :tiempoOferta, :ratio,
                :aluPUsuario, :expY, :expZ)";

        $statement = $connection->prepare($dql);

        $statement->bindValue('nombre', $data['nombre']);
        $statement->bindValue('password', $data['password']);
        $statement->bindValue('idCreador', intval($admin_id));
        $statement->bindValue('fin', $data['fin']->format('Y-m-d H:i:s'));
        $statement->bindValue('maxJugadores', $data['maxJugadores']);
        $statement->bindValue('maxOfertas', $data['maxOfertas']);
        $statement->bindValue('tiempoOferta', $data['tiempoOferta']);
        $statement->bindValue('ratio', $data['ratio']);
        $statement->bindValue('aluPUsuario', $data['aluPUsuario']);
        $statement->bindValue('expY', $data['expY']);
        $statement->bindValue('expZ', $data['expZ']);

        return $statement->execute();
    }

    /**
     * Comprueba una partida dado un identificador y un creador. Retorna los datos de la misma.
     *
     * @param int $idPartida
     * @param int $idAdmin
     *
     * @return array
     */
    public function isMyAdminGame($idPartida, $idAdmin)
    {
        $entityManager = $this->getEntityManager();

        $dql = "SELECT partida
        FROM BaseBundle:Partida partida
        WHERE partida.id = :id AND partida.idCreador = :idCreador";

        $query = $entityManager->createQuery($dql);
        $query->setParameter('id', $idPartida);
        $query->setParameter('idCreador', $idAdmin);

        return $query->getSingleResult();
    }
}