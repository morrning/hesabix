<?php

namespace App\Repository;

use App\Entity\Business;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Business|null find($id, $lockMode = null, $lockVersion = null)
 * @method Business|null findOneBy(array $criteria, array $orderBy = null)
 * @method Business[]    findAll()
 * @method Business[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BusinessRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Business::class);
    }

    // /**
    //  * @return and incerase person number in table
    //  */

    public function getNewNumberPerson($id){
        $temp = $this->createQueryBuilder('t')
            ->where('t.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
        if(! $temp)
            return false;
        $res = $this
            ->createQueryBuilder('f')
            ->update($this->getEntityName(), 'f')
            ->set('f.numPersons', $temp->getNumPersons() + 1)
            ->where('f.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
        return $temp->getNumPersons();
    }
    public function getNewNumberHesabdari($id){
        $temp = $this->createQueryBuilder('t')
            ->where('t.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
        if(! $temp)
            return false;
        $res = $this
            ->createQueryBuilder('f')
            ->update($this->getEntityName(), 'f')
            ->set('f.numHesabdari', $temp->getNumHesabdari() + 1)
            ->where('f.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
        return $temp->getNumPersons();
    }
    public function getNewNumberCommodity($id){
        $temp = $this->createQueryBuilder('t')
            ->where('t.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
        if(! $temp)
            return false;
        $res = $this
            ->createQueryBuilder('f')
            ->update($this->getEntityName(), 'f')
            ->set('f.numCommodity', $temp->getNumCommodity() + 1)
            ->where('f.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
        return $temp->getNumCommodity();
    }

    public function findLast(){
        $res =  $this->createQueryBuilder('p')
            ->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
        return $res[count($res) -1];
    }
}
