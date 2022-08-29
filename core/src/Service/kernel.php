<?php

namespace App\Service;

use App\Entity\Year;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class kernel extends AbstractController
{

    private $em;

    function __construct(EntityManagerInterface  $entityManager)
    {
        $this->em = $entityManager;
    }

    public function checkBID(Request $request){
        $session = $request->getSession();
        if(is_null($session->get('bid'))){
            return false;
        }
        return $session->get('bid');
    }

    public function checkActiveYear(Request $request): Year | bool{
        $session = $request->getSession();
        return $this->em->getRepository('App:Year')->find($session->get('activeYear'));
    }
    public function msgNotActiveYear(){
        $response = [];
        $response['result'] = 0;
        $response['swal'] = [
            'text'=>'سال مالی انتخاب شده بسته شده است.لطفا به آخرین سال مالی رجوع کنید.',
            'confirmButtonText'=>'قبول',
            'icon'=>'error'
        ];
        return $this->json($response);
    }
}