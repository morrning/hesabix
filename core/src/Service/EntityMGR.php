<?php

namespace App\Service;

use phpDocumentor\Reflection\Types\Integer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\EntityManagerInterface;


class EntityMGR
{
    private $em;

    function __construct(EntityManagerInterface  $entityManager)
    {
        $this->em = $entityManager;
    }

    //  DELETE RECORD
    //-----------------------------------------
    public function remove($entityName,$id)
    {
        $res = $this->em->getRepository($entityName)->find($id);
        $this->em->remove($res);
        $this->em->flush();
    }

    // SELECT ENTITY
    //-------------------------------------------
    public function find($entityName,$id)
    {
        return $this->em->getRepository($entityName)->find($id);
    }

    public function findAll($entityName)
    {
        return $this->em->getRepository($entityName)->findAll();
    }

    public function findBy($entity,$params = [],$orders =[])
    {
        return $this->em->getRepository($entity)->findBy($params,$orders);
    }

    public function findOneBy($entity,$params = [])
    {
        return  $this->em->getRepository($entity)->findOneBy($params);
    }

    public function findByPage($entityName,int $page=1,int $perPage=20,$where = null)
    {
        if(is_null($where)){
            return $this->em->createQueryBuilder('q')
                ->select('q')
                ->from($entityName,'q')
                ->setMaxResults($perPage)
                ->setFirstResult($perPage * ($page - 1) )
                ->orderBy('q.id','DESC')
                ->getQuery()
                ->execute();
        }
        return $this->em->createQueryBuilder('q')
            ->select('q')
            ->from($entityName,'q')
            ->setMaxResults($perPage)
            ->setFirstResult($perPage * ($page - 1) )
            ->where($where)
            ->orderBy('q.id','DESC')
            ->getQuery()
            ->execute();
    }

    public function findtop($entityName,$count,$order,$sort = 'DESC')
    {
        return $this->em->createQueryBuilder('q')
            ->select('q')
            ->from($entityName,'q')
            ->setMaxResults($count)
            ->orderBy('q.' . $order,$sort)
            ->getQuery()
            ->getResult();
    }

    //  INSERT/UPDATE ENTITY TO DATABASE
    //------------------------------------------------
    public function insertEntity($entity)
    {
        $this->em->persist($entity);
        $this->em->flush();
        return $entity->getId();
    }

    public function update($entity)
    {
        $this->em->persist($entity);
        $this->em->flush();
    }

    public function rowsCount($entityName){
        return $this->em->createQueryBuilder('n')
            ->select('COUNT(n.id)')
            ->from($entityName,'n')
            ->getQuery()
            ->getSingleScalarResult()
            ;
    }
    // ENTITY MANAGER OBJ
    //------------------------------------------------
    public function getORM()
    {
        $ORM = clone $this->em;
        return $ORM;
    }
}