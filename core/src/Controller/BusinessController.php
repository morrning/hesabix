<?php

namespace App\Controller;

use App\Entity\Business;
use App\Entity\Year;
use App\Form\BusinessNewType;
use App\Service\Log;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BusinessController extends AbstractController
{
    #[Route('/app/main', name: 'app_main' , options: ["expose"=>true])]
    public function app_main(EntityManagerInterface $entityManager): Response
    {
        $buss = $this->getUser()->getBusinesses();
        $perms = $entityManager->getRepository('App:Permission')->getPermissionsbyUser($this->getUser());
        foreach ($perms as $perm)
            $buss[] = $perm->getBid();
        return $this->render('app_main/main.html.twig', [
            'busis' => $buss,
        ]);
    }

    #[Route('/app/business/list', name: 'app_business_list' , options: ["expose"=>true])]
    public function app_business_list(EntityManagerInterface $entityManager): Response
    {
        $buss = $this->getUser()->getBusinesses();
        $perms = $entityManager->getRepository('App:Permission')->getPermissionsbyUser($this->getUser());
        foreach ($perms as $perm)
            $buss[] = $perm->getBid();
        return $this->json([
            'view'=> $this->render('business/list.html.twig', [
                'busis' => $buss,
            ]),
            'title'=>'کسب و کارها'
        ]);
    }

    #[Route('/app/business/new', name: 'app_business_new', options: ["expose"=>true])]
    public function app_business_new(Log $log,Request $request,EntityManagerInterface $entityManager): Response
    {
        $response = [];
        $busi = new Business();
        $form = $this->createForm(BusinessNewType::class,$busi,[
            'action'=>$this->generateUrl('app_business_new')
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //check user has business later
            $bes = $entityManager->getRepository('App:Business')->findOneBy(['owner'=>$this->getUser()]);
            if($bes){
                $response['result'] = 0;
                $response['swal'] = [
                    'text'=>'با توجه به سطح کاربری شما تنها قادر به ایجاد یک کسب و کار هستید.',
                    'confirmButtonText'=>'قبول',
                    'icon'=>'info'
                ];
                $response['component'] = $this->generateUrl('app_business_list');
                return $this->json($response);
            }
            else{
                $busi->setOwner($this->getUser());
                $entityManager->persist($busi);
                $entityManager->flush();
                //create active year
                $year = new Year();
                $year->setActive(true);
                $year->setBid($busi);
                $year->setStart(time());
                $year->setEnd(time() + 31536000);// 31536000 = 1 year
                $year->setName('سال مالی اول');
                $entityManager->persist($year);
                $entityManager->flush();
                $log->add($busi,$this->getUser(),'web','پیکربندی','ایجاد کسب و کار');
                $response['result'] = 1;
                $response['swal'] = [
                    'text'=>'با موفقیت ایجاد شد.',
                    'confirmButtonText'=>'قبول',
                    'icon'=>'success'
                ];
                $response['component'] = $this->generateUrl('app_business_list');
                return $this->json($response);
            }

        }

        return $this->json([
            'view'=>$this->render('business/new.html.twig', [
                'form' => $form->createView(),
            ]),
            'title'=>'کسب و کار جدید'
        ]);
    }

    #[Route('/app/business/view/{id}', name: 'app_business')]
    public function app_business($id,Request $request, EntityManagerInterface $entityManager): Response
    {
        $bid = $entityManager->find('App:Business',$id);
        if(is_null($bid))
            throw $this->createNotFoundException();

        $session = $request->getSession();
        $session->set('bid',$id);
        $year = $entityManager->getRepository('App:Year')->findOneBy(['active'=>true,'bid'=>$bid]);
        if(! $year){
            $year = new Year();
            $year->setActive(true);
            $year->setBid($bid);
            $year->setStart(time());
            $year->setEnd(time() + 31536000);// 31536000 = 1 year
            $year->setName('سال مالی اول');
            $entityManager->persist($year);
            $entityManager->flush();
        }
        if($session->get('activeYear') == null){
            $yearSelected = $entityManager->getRepository('App:Year')->findOneBy(['active'=>true,'bid'=>$bid]);
            $session->set('activeYear',$year->getId());
        }
        else{
            $yearSelected = $entityManager->getRepository('App:Year')->findOneBy(['id'=>$session->get('activeYear'),'bid'=>$bid]);
        }

        return $this->render('app_main/base.html.twig', [
            'bid' => $id,
            'year'=>$year,
            'yearSelected' => $yearSelected,
            'yrs'=>$entityManager->getRepository('App:Year')->findBy(['bid'=>$bid])
        ]);
    }
}
