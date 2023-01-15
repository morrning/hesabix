<?php

namespace App\Service;

use App\Entity\Year;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class kernel extends AbstractController
{

    private $em;
    private $requestStack;
    private $request;

    function __construct(EntityManagerInterface  $entityManager,RequestStack $requestStack)
    {
        $this->em = $entityManager;
        $this->requestStack = $requestStack;
        $this->request = $requestStack->getCurrentRequest();
    }

    public function checkBID(Request $request){
        $session = $request->getSession();
        if(is_null($session->get('bid'))){
            return false;
        }
        return $session->get('bid');
    }

    public function checkActiveYear(Request $request = null): Year | bool{
        if($request)
            $session = $request->getSession();
        else
            $session = $this->request->getSession();

        return $this->em->getRepository(\App\Entity\Year::class)->find($session->get('activeYear'));
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