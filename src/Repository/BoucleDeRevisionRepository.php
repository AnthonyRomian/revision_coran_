<?php

namespace App\Repository;

use App\Entity\BoucleDeRevision;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BoucleDeRevision|null find($id, $lockMode = null, $lockVersion = null)
 * @method BoucleDeRevision|null findOneBy(array $criteria, array $orderBy = null)
 * @method BoucleDeRevision[]    findAll()
 * @method BoucleDeRevision[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BoucleDeRevisionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BoucleDeRevision::class);
    }

    // /**
    //  * @return BoucleDeRevision[] Returns an array of BoucleDeRevision objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BoucleDeRevision
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
