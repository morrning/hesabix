<?php

namespace App\Repository;

use App\Entity\HbuyPay;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method HbuyPay|null find($id, $lockMode = null, $lockVersion = null)
 * @method HbuyPay|null findOneBy(array $criteria, array $orderBy = null)
 * @method HbuyPay[]    findAll()
 * @method HbuyPay[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HbuyPayRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HbuyPay::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(HbuyPay $entity, bool $flush = true): void
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
    public function remove(HbuyPay $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return HbuyPay[] Returns an array of HbuyPay objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('h.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?HbuyPay
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
