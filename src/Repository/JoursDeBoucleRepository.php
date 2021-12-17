<?php

namespace App\Repository;

use App\Entity\JoursDeBoucle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method JoursDeBoucle|null find($id, $lockMode = null, $lockVersion = null)
 * @method JoursDeBoucle|null findOneBy(array $criteria, array $orderBy = null)
 * @method JoursDeBoucle[]    findAll()
 * @method JoursDeBoucle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JoursDeBoucleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JoursDeBoucle::class);
    }

    // /**
    //  * @return JoursDeBoucle[] Returns an array of JoursDeBoucle objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('j.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?JoursDeBoucle
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
