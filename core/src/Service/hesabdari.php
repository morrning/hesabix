<?php

namespace App\Service;

use App\Entity\HesabdariFile;
use Doctrine\ORM\EntityManagerInterface;

class hesabdari
{
private $em;
private $kernel;
    function __construct(EntityManagerInterface  $entityManager,kernel $kernel)
    {
        $this->em = $entityManager;
        $this->kernel = $kernel;
    }

    public function insertNewDoc($submitter,$date,$des,$bid,$ref,$items){
        $file = new HesabdariFile();
        $file->setSubmitter($submitter);
        $file->setDes($des);
        $file->setDate($date);
        $file->setBid($bid);
        $file->setRef($ref);
        $file->setYear($this->kernel->checkActiveYear());
        $file->setArzType($bid->getArzMain());
        $file->setNum($this->em->getRepository('App:Business')->getNewNumberHesabdari($bid->getId()));
        $this->em->persist($file);
        $this->em->flush();
        foreach ($items as $item){
            $item->setFile($file);
            $this->em->persist($item);
            $this->em->flush();
        }
        return $file;
    }

    public function removeByRef($part,$bid,$id){
        $item = $this->em->getRepository('App:HesabdariFile')->findOneBy(['ref'=>$part . ':' . $bid . ':' . $id]);
        if($item){
            $this->em->getRepository('App:HesabdariFile')->remove($item);
            return true;
        }
        return false;
    }

    public function getBalance($ref){
        $res = $this->getTransactionsByRef($ref);
        $bd = 0;
        $bs = 0;
        foreach ($res as $re){
            $bd += $re->getBd();
            $bs += $re->getBs();
        }
        return $bs - $bd;
    }

    public function getTransactionsByRef($ref){
        $params = explode(':',$ref);
        return $this->em->getRepository('App:HesabdariItem')->findBy([
            'type'=>$params[0],
            'typeData'=>$params[2]
        ]);
    }

    public function getHesabdariFileBalance(HesabdariFile $file): int{
        $items = $this->em->getRepository('App:HesabdariItem')->findBy(['file' => $file]);
        $amount = 0;
        foreach($items as $item){
            $amount += $item->getBs();
        }
        return $amount;
    }
}