<?php

namespace App\Repository;

use App\Entity\Cost;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Cost|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cost|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cost[]    findAll()
 * @method Cost[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cost::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Cost $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Cost $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function getListAll($bid,$year)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.bid = :val')
            ->andWhere('p.year = :year')
            ->setParameters(['val'=> $bid,'year'=>$year])
            ->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }
    // /**
    //  * @return Cost[] Returns an array of Cost objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Cost
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
