<?php

namespace App\Repository;

use App\Entity\HesabdariRefs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method HesabdariRefs|null find($id, $lockMode = null, $lockVersion = null)
 * @method HesabdariRefs|null findOneBy(array $criteria, array $orderBy = null)
 * @method HesabdariRefs[]    findAll()
 * @method HesabdariRefs[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HesabdariRefsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HesabdariRefs::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(HesabdariRefs $entity, bool $flush = true): void
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
    public function remove(HesabdariRefs $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return HesabdariRefs[] Returns an array of HesabdariRefs objects
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
    public function findOneBySomeField($value): ?HesabdariRefs
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
