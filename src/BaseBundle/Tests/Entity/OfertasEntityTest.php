<?php

namespace BaseBundle\Tests\Entity;

class OfertasEntityTest extends Entity
{


    public function testOferta()
    {
        parent::mock();

        $this->repository->expects($this->once())
            ->method('find')
            ->will($this->returnValue($this->oferta));

        $this->emMod->expects($this->once())
            ->method('getRepository')
            ->will($this->returnValue($this->repository));

        $ofertas = $this->emMod->getRepository('BaseBundle:Ofertas')->find(0);

        $this->assertEquals(10, $ofertas->getAluBlancaIn());
        $ofertas->setAluBlancaIn(123456789);
        $ofertas->setAlurojaIn(987654321);
        $this->assertEquals(123456789,$ofertas->getAluBlancaIn());
        $this->assertEquals(987654321,$ofertas->getAlurojaIn());

    }

    public function testSetNewOffer()
    {
        $data = ['aluBlancaIn' => 112345, 'aluBlancaOut' => 98745621, 'aluRojaIn' => 854132655, 'aluRojaOut' => 12547851];
        $this->em->getRepository('BaseBundle:Ofertas')->SetNewOffer(2, 7, 1, $data);

        $result = $this->selectOferta();
        $this->assertEquals(112345, $result->getAluBlancaIn());
    }

    public function testUpdateStatus()
    {
        $result = $this->selectOferta();
        $oferta = $this->em->getRepository('BaseBundle:Ofertas')->updateStatus(0, $result->getId());
        $this->assertEquals(0, $oferta[0]['estado']);
    }


    public function testCountOffers()
    {
        $ofertas = $this->em->getRepository('BaseBundle:Ofertas')->countOffers(2, 1);
        $this->assertTrue(count($ofertas) > 0);
    }

    public function testCheckDeal()
    {
        $result = $this->selectOferta();
        $oferta = $this->em->getRepository('BaseBundle:Ofertas')->checkDeal(2, 1, $result->getId(), 7);
        $this->assertTrue(count($oferta) > 0);
    }

    public function testFindAllGameDeals(){
        $result = $this->selectOferta();
        $result->setEstado(1);
        $this->em->flush();

        $ofertas = $this->em->getRepository('BaseBundle:Ofertas')->findAllGameDeals(1);

        $result->setEstado(0);
        $this->em->flush();

        $this->assertTrue(count($ofertas) > 0);

    }

    public function testDeleteDeal()
    {
        $result = $this->selectOferta();
        $this->em->getRepository('BaseBundle:Ofertas')->deleteDeal(2, 1, $result->getId(), 7);

        $qb = $this->em->createQueryBuilder();
        $qb->select('o')
            ->from('BaseBundle:Ofertas', 'o')
            ->where($qb->expr()->andX(
                $qb->expr()->eq('o.aluBlancaIn', '?1'),
                $qb->expr()->eq('o.aluBlancaOut', '?2'),
                $qb->expr()->eq('o.aluRojaIn', '?3'),
                $qb->expr()->eq('o.aluRojaOut', '?4')
            ))
            ->setParameter(1, 112345)
            ->setParameter(2, 98745621)
            ->setParameter(3, 854132655)
            ->setParameter(4, 12547851);

        $query = $qb->getQuery();
        $ofertas = $query->getResult();

        $this->assertFalse(count($ofertas) > 0);

    }

    private function selectOferta()
    {
        $qb = $this->em->createQueryBuilder();

        $qb->select('o')
            ->from('BaseBundle:Ofertas', 'o')
            ->where($qb->expr()->andX(
                $qb->expr()->eq('o.aluBlancaIn', '?1'),
                $qb->expr()->eq('o.aluBlancaOut', '?2'),
                $qb->expr()->eq('o.aluRojaIn', '?3'),
                $qb->expr()->eq('o.aluRojaOut', '?4')
            ))
            ->setParameter(1, 112345)
            ->setParameter(2, 98745621)
            ->setParameter(3, 854132655)
            ->setParameter(4, 12547851);

        $query = $qb->getQuery();
        return $this->ofertaSingle = $query->getSingleResult();
    }
}