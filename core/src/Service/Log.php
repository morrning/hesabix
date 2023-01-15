<?php

namespace App\Service;

use App\Entity\Business;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class Log
{
    private $em;

    function __construct(EntityManagerInterface  $entityManager)
    {
        $this->em = $entityManager;
    }
    public function add(Business $business,User $user,$device,$part,$des): bool
    {
        $log = new \App\Entity\Log();
        $log->setBid($business);
        $log->setUser($user);
        $log->setDateSubmit(time());
        $log->setDevice($device);
        $log->setPart($part);
        $log->setDes($des);
        $this->em->getRepository(\App\Entity\Log::class)->add($log);
        return true;
    }
}