<?php

namespace App\Repository;

use App\Entity\Hbuy;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Hbuy|null find($id, $lockMode = null, $lockVersion = null)
 * @method Hbuy|null findOneBy(array $criteria, array $orderBy = null)
 * @method Hbuy[]    findAll()
 * @method Hbuy[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HbuyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Hbuy::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Hbuy $entity, bool $flush = true): void
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
    public function remove(Hbuy $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }
    public function getListAll($bid)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.bid = :val')
            ->setParameter('val', $bid)
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }
    // /**
    //  * @return Hbuy[] Returns an array of Hbuy objects
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
    public function findOneBySomeField($value): ?Hbuy
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
