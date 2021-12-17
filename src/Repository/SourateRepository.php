<?php

namespace App\Repository;

use App\Entity\Sourate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Sourate|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sourate|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sourate[]    findAll()
 * @method Sourate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SourateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sourate::class);
    }

    // /**
    //  * @return Sourate[] Returns an array of Sourate objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Sourate
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
