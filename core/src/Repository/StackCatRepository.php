<?php

namespace App\Repository;

use App\Entity\StackCat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method StackCat|null find($id, $lockMode = null, $lockVersion = null)
 * @method StackCat|null findOneBy(array $criteria, array $orderBy = null)
 * @method StackCat[]    findAll()
 * @method StackCat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StackCatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StackCat::class);
    }

    // /**
    //  * @return StackCat[] Returns an array of StackCat objects
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
    public function findOneBySomeField($value): ?StackCat
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
