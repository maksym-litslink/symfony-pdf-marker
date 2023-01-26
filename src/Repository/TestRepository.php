<?php

namespace App\Repository;

use App\Entity\Pdf;
use App\Entity\Test;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Test>
 *
 * @method Test|null find($id, $lockMode = null, $lockVersion = null)
 * @method Test|null findOneBy(array $criteria, array $orderBy = null)
 * @method Test[]    findAll()
 * @method Test[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Test::class);
    }

    public function add(Test $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Test $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function removeByPdf(Pdf $pdf): void
    {
        $this->createQueryBuilder('t')
            ->delete()
            ->where('t.pdf = :pdf')
            ->setParameter('pdf', $pdf)
            ->getQuery()
            ->execute()
        ;
    }

    public function removeWithNull(): void
    {
        $this->createQueryBuilder('t')
            ->delete()
            ->where('t.pdf IS NULL')
            ->getQuery()
            ->execute()
        ;
    }
}
