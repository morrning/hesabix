<?php

namespace App\Repository;

use App\Entity\BanksTransfer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BanksTransfer|null find($id, $lockMode = null, $lockVersion = null)
 * @method BanksTransfer|null findOneBy(array $criteria, array $orderBy = null)
 * @method BanksTransfer[]    findAll()
 * @method BanksTransfer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BanksTransferRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BanksTransfer::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(BanksTransfer $entity, bool $flush = true): void
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
    public function remove(BanksTransfer $entity, bool $flush = true): void
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
    //  * @return BanksTransfer[] Returns an array of BanksTransfer objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BanksTransfer
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
