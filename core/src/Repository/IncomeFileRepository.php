<?php

namespace App\Repository;

use App\Entity\IncomeFile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method IncomeFile|null find($id, $lockMode = null, $lockVersion = null)
 * @method IncomeFile|null findOneBy(array $criteria, array $orderBy = null)
 * @method IncomeFile[]    findAll()
 * @method IncomeFile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IncomeFileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, IncomeFile::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(IncomeFile $entity, bool $flush = true): void
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
    public function remove(IncomeFile $entity, bool $flush = true): void
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
    //  * @return IncomeFile[] Returns an array of IncomeFile objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?IncomeFile
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
