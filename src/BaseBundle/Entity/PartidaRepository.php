<?php

namespace BaseBundle\Entity;

use Doctrine\ORM\EntityRepository;

class PartidaRepository extends EntityRepository
{

    public function findAdminPardidas($admin_id)
    {
        $entityManager = $this->getEntityManager();

        $dql = "SELECT partida.nombre, partida.fin,partida.creado,partida.id FROM BaseBundle:Partida partida
        WHERE partida.idCreador = :id";

        $query = $entityManager->createQuery($dql);
        $query->setParameter('id', $admin_id);

        return $query->getResult();
    }

    public function SetNewPartida($data, $admin_id)
    {
        $entityManager = $this->getEntityManager();
        $connection = $entityManager->getConnection();

        $dql = "INSERT INTO partida (nombre, creado, id_creador, fin, max_jugadores, max_ofertas, tiempo_oferta,
                alg_utilidad, alg_reparto, alu_roja, alu_blanca)
                VALUES (:nombre, now(), :idCreador, :fin, :maxJugadores, :maxOfertas, :tiempoOferta, :algUtilidad,
                :algReparto, :aluRoja, :aluBlanca)";

        $statement = $connection->prepare($dql);

        $statement->bindValue('nombre', $data['nombre']);
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
}