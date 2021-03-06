<?php


namespace BaseBundle\Entity;

use BaseBundle\Controller\Logic\OfertaLogic;
use Doctrine\ORM\EntityRepository;

/**
 * Oferta.estado
 *      0 -> no tratada
 *      1 -> aceptada
 *      2 -> rechazada
 *      3 -> Oferta concluida
 *
 * Class OfertasRepository
 * @package BaseBundle\Entity
 */
class OfertasRepository extends EntityRepository
{
    /**
     * Guarda una oferta
     *
     * @param int $user_id
     * @param int $idPlayer
     * @param int $id_partida
     * @param array $data
     * @return bool
     * @throws \Doctrine\DBAL\DBALException
     */
    public function SetNewOffer($user_id, $idPlayer, $id_partida, $data)
    {
        $entityManager = $this->getEntityManager();
        $connection = $entityManager->getConnection();

        $dql = "INSERT INTO ofertas (idPartida, idCreador, idDestinatario, creado, estado, aluBlancaIn, aluRojaIn, aluBlancaOut, aluRojaOut)
                VALUES (:idPartida, :userId, :idPlayer, now(),:estado, :aluBlancaIn, :aluRojaIn, :aluBlancaOut, :aluRojaOut)";

        $statement = $connection->prepare($dql);

        $statement->bindValue('userId', $user_id);
        $statement->bindValue('idPlayer', intval($idPlayer));
        $statement->bindValue('estado', OfertaLogic::NOTRATADA);
        $statement->bindValue('idPartida', intval($id_partida));
        $statement->bindValue('aluBlancaIn', $data['aluBlancaIn']);
        $statement->bindValue('aluBlancaOut', $data['aluBlancaOut']);
        $statement->bindValue('aluRojaIn', $data['aluRojaIn']);
        $statement->bindValue('aluRojaOut', $data['aluRojaOut']);

        return $statement->execute();
    }

    /**
     * Actualiza el estado de una partida y la fecha de modificación
     *
     * @param int $estado
     * @param int $idOferta
     * @return array
     */
    public function updateStatus($estado, $idOferta)
    {
        $entityManager = $this->getEntityManager();

        $dql = "UPDATE BaseBundle:Ofertas ofertas
                SET ofertas.estado = :estado, ofertas.modificado = CURRENT_TIMESTAMP()
                WHERE ofertas.id = :idOferta";

        $query = $entityManager->createQuery($dql);
        $query->setParameter('estado', $estado);
        $query->setParameter('idOferta', $idOferta);

        return $query->getResult();
    }

    /**
     * Ofertas en curso de un usuario
     *
     * @param int $idUser
     * @param int $idPartida
     * @return array
     */
    public function currentOffers($idUser, $idPartida)
    {
        $entityManager = $this->getEntityManager();

        $dql = "SELECT ofertas
                FROM BaseBundle:Ofertas ofertas
                JOIN BaseBundle:Partida partida WITH partida.id = ofertas.idPartida
                WHERE ofertas.idCreador = :idUser AND ofertas.idPartida = :idPartida AND ofertas.estado = :estado";

        $query = $entityManager->createQuery($dql);
        $query->setParameter('idUser', $idUser);
        $query->setParameter('idPartida', $idPartida);
        $query->setParameter('estado', OfertaLogic::NOTRATADA);

        return $query->getResult();
    }

    /**
     * Comprueba que la oferta buscada existe con los datos recibidos
     *
     * @param int $idCreador
     * @param int $idPartida
     * @param int $idOferta
     * @param int $idDestinatario
     * @return Ofertas
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function checkDeal($idCreador, $idPartida, $idOferta, $idDestinatario)
    {
        $entityManager = $this->getEntityManager();

        $dql = "SELECT ofertas
                FROM BaseBundle:Ofertas ofertas
                WHERE ofertas.idCreador = :idCreador AND ofertas.idPartida = :idPartida
                AND ofertas.idDestinatario = :idDestinatario
                AND ofertas.id = :id";

        $query = $entityManager->createQuery($dql);
        $query->setParameter('idCreador', $idCreador);
        $query->setParameter('idPartida', $idPartida);
        $query->setParameter('idDestinatario', $idDestinatario);
        $query->setParameter('id', $idOferta);

        return $query->getSingleResult();
    }

    /**
     * Eliminar una deterinada oferta
     *
     * @param int $idCreador
     * @param int $idPartida
     * @param int $idOferta
     * @param int $idDestinatario
     * @return array
     */
    public function deleteDeal($idCreador, $idPartida, $idOferta, $idDestinatario)
    {
        $entityManager = $this->getEntityManager();

        $dql = "DELETE
                FROM BaseBundle:Ofertas ofertas
                WHERE ofertas.idCreador = :idCreador AND ofertas.idPartida = :idPartida
                AND ofertas.idDestinatario = :idDestinatario
                AND ofertas.id = :id AND ofertas.estado = :estado";

        $query = $entityManager->createQuery($dql);
        $query->setParameter('idCreador', $idCreador);
        $query->setParameter('idPartida', $idPartida);
        $query->setParameter('idDestinatario', $idDestinatario);
        $query->setParameter('id', $idOferta);
        $query->setParameter('estado', OfertaLogic::NOTRATADA);

        return $query->getResult();
    }

    /**
     * Comprueba ofertas enviadas por idCreador
     *
     * @param int $idCreador
     * @param int $idPartida
     * @return array
     */
    public function findSentOffers($idCreador, $idPartida, $estado)
    {
        $entityManager = $this->getEntityManager();

        $dql = "SELECT  IDENTITY(ofertas.idDestinatario) AS idDestinatario, ofertas.aluBlancaIn, ofertas.aluRojaIn, ofertas.modificado,
                ofertas.modificado,ofertas.aluBlancaOut, ofertas.aluRojaOut, ofertas.creado, ofertas.id,
                IDENTITY(ofertas.idCreador) AS idCreador, partida.tiempoOferta,  partida.id AS idPartida, user.username
                FROM BaseBundle:Ofertas ofertas
                JOIN BaseBundle:Partida partida WITH partida.id = ofertas.idPartida
                JOIN BaseBundle:User user WITH user.id = ofertas.idDestinatario
                WHERE ofertas.idCreador = :idCreador AND ofertas.estado = :estado
                AND ofertas.idPartida = :idPartida";

        $query = $entityManager->createQuery($dql);
        $query->setParameter('idCreador', $idCreador);
        $query->setParameter('idPartida', $idPartida);
        $query->setParameter('estado', $estado);

        return $query->getResult();
    }


    /**
     * Comprueba Ofertas recibidas por idDestinatario
     *
     * @param int $idDestinatario
     * @param int $idPartida
     * @return array
     */
    public function findRecievedOffers($idDestinatario, $idPartida, $estado)
    {
        $entityManager = $this->getEntityManager();

        $dql = "SELECT IDENTITY(ofertas.idCreador) AS idCreador, ofertas.aluBlancaIn, ofertas.aluRojaIn, ofertas.aluBlancaOut,
                ofertas.aluRojaOut, ofertas.creado, ofertas.id, IDENTITY(ofertas.idDestinatario) AS idDestinatario,
                ofertas.modificado, partida.tiempoOferta, partida.id AS idPartida, user.username
                FROM BaseBundle:Ofertas ofertas
                JOIN BaseBundle:Partida partida WITH partida.id = ofertas.idPartida
                JOIN BaseBundle:User user WITH user.id = ofertas.idCreador
                WHERE ofertas.idDestinatario = :idDestinatario AND ofertas.estado = :estado
                AND ofertas.idPartida = :idPartida";

        $query = $entityManager->createQuery($dql);
        $query->setParameter('idDestinatario', $idDestinatario);
        $query->setParameter('idPartida', $idPartida);
        $query->setParameter('estado', $estado);

        return $query->getResult();
    }

    /**
     * Todas las ofertas que se han hecho en una partida.
     *
     * @param int $idPartida
     * @return array
     */
    public function findAllGameDeals($idPartida, $estado)
    {
        $entityManager = $this->getEntityManager();

        $dql = "SELECT ofertas
                FROM BaseBundle:Ofertas ofertas
                WHERE ofertas.idPartida = :idPartida AND ofertas.estado = :estado";

        $query = $entityManager->createQuery($dql);
        $query->setParameter('idPartida', $idPartida);
        $query->setParameter('estado', $estado);

        return $query->getResult();
    }

    /**
     * @param int $idUser
     * @param int $idPartida
     * @return array
     * @internal param User $user
     */
    public function findAllUserGameDeals($idUser, $idPartida){
        $entityManager = $this->getEntityManager();

        $dql = "SELECT ofertas FROM BaseBundle:Ofertas ofertas WHERE
                (ofertas.idCreador = :userId OR ofertas.idDestinatario = :userId ) AND ofertas.idPartida = :idPartida
                AND ofertas.estado = :estado
                ORDER BY ofertas.modificado ASC";

        $query = $entityManager->createQuery($dql);
        $query->setParameter('userId', $idUser);
        $query->setParameter('idPartida', $idPartida);
        $query->setParameter('estado', OfertaLogic::ACEPTADA);

        return $query->getResult();
    }
}