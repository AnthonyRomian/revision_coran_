<?php

namespace App\Repository;

use App\Entity\Verset;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Verset|null find($id, $lockMode = null, $lockVersion = null)
 * @method Verset|null findOneBy(array $criteria, array $orderBy = null)
 * @method Verset[]    findAll()
 * @method Verset[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VersetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Verset::class);
    }

    // /**
    //  * @return Verset[] Returns an array of Verset objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Verset
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
