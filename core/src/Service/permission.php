<?php

namespace App\Service;

use App\Entity\Business;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class permission
{
    private $em;

    function __construct(EntityManagerInterface  $entityManager)
    {
        $this->em = $entityManager;
    }

    //this function check user has permission for access some part
    public function hasPermission($permissionName,Business $bid, User $user){
        //check for that user is owner of bussiness
        if($bid->getOwner() == $user)
            return true;
        $persission = $this->em->getRepository('App:Permission')->getPersissions($bid,$user);
        if($persission){
            if($persission->{'get' . ucfirst($permissionName)}())
                return true;
        }
        return false;
    }

    //this function check user has permission for access some part
    public function hasPermissionInCurrentBusiness($permissionName,string $id, User $user){
        //check for that user is owner of business
        $bid = $this->em->getRepository('App:Business')->find($id);
        if($bid->getOwner() == $user)
            return true;
        $persission = $this->em->getRepository('App:Permission')->getPersissions($bid,$user);
        if($persission){
            if($persission->{'get' . ucfirst($permissionName)}())
                return true;
        }
        return false;
    }


}