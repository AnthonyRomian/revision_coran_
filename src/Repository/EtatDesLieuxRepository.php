<?php

namespace App\Repository;

use App\Entity\EtatDesLieux;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EtatDesLieux|null find($id, $lockMode = null, $lockVersion = null)
 * @method EtatDesLieux|null findOneBy(array $criteria, array $orderBy = null)
 * @method EtatDesLieux[]    findAll()
 * @method EtatDesLieux[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EtatDesLieuxRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EtatDesLieux::class);
    }

    // /**
    //  * @return EtatDesLieux[] Returns an array of EtatDesLieux objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?EtatDesLieux
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
