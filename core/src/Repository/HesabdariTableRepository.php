<?php

namespace App\Repository;

use App\Entity\HesabdariTable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method HesabdariTable|null find($id, $lockMode = null, $lockVersion = null)
 * @method HesabdariTable|null findOneBy(array $criteria, array $orderBy = null)
 * @method HesabdariTable[]    findAll()
 * @method HesabdariTable[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HesabdariTableRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HesabdariTable::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(HesabdariTable $entity, bool $flush = true): void
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
    public function remove(HesabdariTable $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return HesabdariTable[] Returns an array of HesabdariTable objects
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
    public function findOneBySomeField($value): ?HesabdariTable
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
