<?php

namespace App\Repository;

use App\Entity\StackContent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method StackContent|null find($id, $lockMode = null, $lockVersion = null)
 * @method StackContent|null findOneBy(array $criteria, array $orderBy = null)
 * @method StackContent[]    findAll()
 * @method StackContent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StackContentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StackContent::class);
    }

    public function findtop($count)
    {
        return $this->createQueryBuilder('n')
            ->where('n.upperID is NULL')
            ->orderBy('n.id', 'DESC')
            ->setMaxResults($count)
            ->getQuery()
            ->getResult()
            ;
    }

    // /**
    //  * @return StackContent[] Returns an array of StackContent objects
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
    public function findOneBySomeField($value): ?StackContent
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
