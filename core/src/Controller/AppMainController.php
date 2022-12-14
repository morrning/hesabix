<?php

namespace App\Controller;

use App\Entity\Business;
use App\Entity\Permission;
use App\Entity\Year;
use App\Form\BusinessNewType;
use App\Form\PermissionType;
use App\Form\PersonRSCompactType;
use App\Kernel;
use App\Service\Log;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Storage\PhpBridgeSessionStorage;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;

class AppMainController extends AbstractController
{
    private $bid = null;
    private $request = null;
    private $bidObject = null;
    private $activeYear = null;
    private $activeYearObject = null;
    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager, \App\Service\kernel $kernel)
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->bid = $kernel->checkBID($this->request);
        if(!$this->bid){
            throw $this->createNotFoundException();
        }
        $this->bidObject = $entityManager->getRepository('App:Business')->find($this->bid);
        if (! $this->bidObject)
            throw $this->createNotFoundException();
        $this->activeYear = $kernel->checkActiveYear($this->request);
        if(!$this->activeYear){
            throw $this->createNotFoundException();
        }
        $this->activeYearObject = $entityManager->getRepository('App:Year')->find($this->activeYear);
    }



    #[Route('/app/business/edit', name: 'app_business_edit', options: ["expose"=>true])]
    public function app_business_edit(\App\Service\permission $permission,Log $log,Request $request,EntityManagerInterface $entityManager): Response
    {
        if(! $permission->hasPermission('admin',$this->bidObject,$this->getUser()))
            throw $this->createAccessDeniedException();
        $response = [];
        $id = $this->bid;
        $busi = $entityManager->getRepository('App:Business')->find($id);
        if(! $busi)
            throw $this->createNotFoundException();
        $form = $this->createForm(BusinessNewType::class,$busi,[
            'action'=>$this->generateUrl('app_business_edit',['id'=>$this->bid])
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($busi);
            $entityManager->flush();
            $log->add($this->bidObject,$this->getUser(),'web','????????????????','???????????? ?????? ?? ??????');

            $response['result'] = 1;
            $response['swal'] = [
                'text'=>'???? ???????????? ???????????? ????.',
                'confirmButtonText'=>'????????',
                'icon'=>'success'
            ];
            return $this->json($response);
        }

        return $this->json([
            'view'=>$this->render('business/new.html.twig', [
                'form' => $form->createView(),
            ]),
            'title'=>'???????????? ?????????????? ?????? ?? ??????'
        ]);
    }

    #[Route('/app/business/change/year/{id}/{year}', name: 'app_change_year', options: ["expose"=>true])]
    public function app_change_year($id,$year,Request $request, EntityManagerInterface $entityManager): Response
    {
        $bid = $entityManager->find('App:Business',$id);
        if(is_null($bid))
            throw $this->createNotFoundException();

        $session = $request->getSession();
        $session->set('bid',$id);
        $yearobj = $entityManager->getRepository('App:Year')->find($year);
        if($yearobj){
            if($yearobj->getBid() == $bid){
                $session->set('activeYear',$yearobj->getId());
            }
        }

        $response['result'] = 1;
        $response['swal'] = [
            'text'=>'?????? ???????? ???? ?????????? ????????',
            'confirmButtonText'=>'????????',
            'icon'=>'success'
        ];
        $response['swal']['reload'] = 1;
        return $this->json($response);
    }

    #[Route('/app/dashboard', name: 'app_dashboard', options: ["expose"=>true])]
    public function app_dashboard(\App\Service\permission $permission,Request $request, \App\Service\kernel $kernel,EntityManagerInterface $entityManager): Response
    {
        if(! $permission->hasPermission('view',$this->bidObject,$this->getUser()))
            throw $this->createAccessDeniedException();
        $bid = $kernel->checkBID($request);
        if(!$bid){
            return $this->redirectToRoute('app_main');
        }
        $costSum = 0;
        $costs = $entityManager->getRepository('App:Cost')->getListAll($this->bid,$this->activeYear);
        foreach ($costs as $cost){
            $costSum += $cost->getAmount();
        }
        $incomeSum = 0;
        $incomes = $entityManager->getRepository('App:IncomeFile')->getListAll($this->bid,$this->activeYear);
        foreach ($incomes as $income){
            $incomeSum += $income->getAmount();
        }
        return $this->json(
            [
                'view'=>$this->render('app_main/dashboard.html.twig', [
                    'persons' => $entityManager->getRepository('App:Person')->findBy(['bid'=>$bid]),
                    'banks' => $entityManager->getRepository('App:BanksAccount')->findBy(['bussiness'=>$bid]),
                    'commodity' => $entityManager->getRepository('App:Commodity')->findBy(['bid'=>$bid]),
                    'costSum'=>$costSum,
                    'costs'=> $entityManager->getRepository('App:Cost')->findBy(['bid'=>$this->bid,'year'=>$this->activeYear],['id'=>'DESC'],5),
                    'incomeSum'=>$incomeSum,
                    'incomes'=> $entityManager->getRepository('App:IncomeFile')->findBy(['bid'=>$this->bid,'year'=>$this->activeYear],['id'=>'DESC'],5),
                    'bid'=>$this->bidObject
                ]),
                'title'=>'??????????????'
            ]
        );
    }

    #[Route('/app/permissions/list', name: 'app_permissions_list' , options: ["expose"=>true])]
    public function app_permissions_list(\App\Service\permission $permission,Request $request,EntityManagerInterface $entityManager): Response
    {
        if(! $permission->hasPermission('admin',$this->bidObject,$this->getUser()))
            throw $this->createAccessDeniedException();
        $perms = $entityManager->getRepository('App:Permission')->getPermissionsbyBusiness($this->bidObject);
        return $this->json([
            'view'=> $this->render('business/permissions/list.html.twig', [
                'perms' => $perms,
                'owner' => $this->bidObject->getOwner()
            ]),
            'title'=>'?????????????? ?? ???????? ????????????'
        ]);
    }

    #[Route('/app/permissions/add', name: 'app_permissions_add' , options: ["expose"=>true])]
    public function app_permissions_add(\App\Service\permission $permission,Log $log,Request $request,EntityManagerInterface $entityManager): Response
    {
        if(! $permission->hasPermission('admin',$this->bidObject,$this->getUser()))
            throw $this->createAccessDeniedException();
       $email = $request->get('email');
       $user = $entityManager->getRepository('App:User')->findOneBy(['email'=>$email]);
       if(!$user){
           return $this->json([
                'swal'=>[
                    'text'=>'???????????? ???? ?????????? ???????? ?????? ???????? ??????.',
                    'confirmButtonText'=>'????????',
                    'icon'=>'error'
                ]
           ]);
       }
       //check permission exist before
        $perm = $entityManager->getRepository('App:Permission')->findOneBy(['bid'=>$this->bid,'user'=>$user]);
       if((!$perm) && ($this->bidObject->getOwner() != $user)){
           $perm = new Permission();
           $perm->setBid($this->bidObject);
           $perm->setUser($user);
           $perm->setView(true);
           $entityManager->getRepository('App:Permission')->add($perm);
           $log->add($this->bidObject,$this->getUser(),'web','????????????????','???????????? ???????????? ?????????? ???? ??????????: ' . $perm->getUser()->getEmail());

           return $this->json([
               'swal'=>[
                   'text'=>'?????????? ???? ???????????? ???????????? ????',
                   'confirmButtonText'=>'????????',
                   'icon'=>'success'
               ],
               'component'=> $this->generateUrl('app_permissions_list')
           ]);
       }
        return $this->json([
            'swal'=>[
                'text'=>'?????????? ???????? ???????????? ?????? ??????.',
                'confirmButtonText'=>'????????',
                'icon'=>'info'
            ],
            'component'=> $this->generateUrl('app_permissions_list'),
            'clear'=>true
        ]);
    }

    #[Route('/app/permissions/delete/{id}', name: 'app_permissions_delete' , options: ["expose"=>true])]
    public function app_permissions_delete($id,\App\Service\permission $permission,Log $log,Request $request,EntityManagerInterface $entityManager): Response
    {
        if(! $permission->hasPermission('admin',$this->bidObject,$this->getUser()))
            throw $this->createAccessDeniedException();

        $perm = $entityManager->getRepository('App:Permission')->find($id);
        if($perm)
            $entityManager->getRepository('App:Permission')->remove($perm);
        $log->add($this->bidObject,$this->getUser(),'web','????????????????','?????? ???????????? ?????????? ???? ??????????: ' . $perm->getUser()->getEmail());

        return $this->json([
            'result'=>1
        ]);
    }

    #[Route('/app/permissions/edit/{id}', name: 'app_permissions_edit' , options: ["expose"=>true])]
    public function app_permissions_edit($id,\App\Service\permission $permission,Log $log,Request $request,EntityManagerInterface $entityManager): Response
    {
        if(! $permission->hasPermission('admin',$this->bidObject,$this->getUser()))
            throw $this->createAccessDeniedException();

        $perm = $entityManager->getRepository('App:Permission')->find($id);
        if($perm->getBid() != $this->bidObject)
            throw $this->createAccessDeniedException();
        $form = $this->createForm(PermissionType::class,$perm,[
            'action'=>$this->generateUrl('app_permissions_edit',['id'=>$id]),
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($perm);
            $entityManager->flush();
            $log->add($this->bidObject,$this->getUser(),'web','????????????????','???????????? ???????????? ?????????? ???? ??????????: ' . $perm->getUser()->getEmail());
            return $this->json([
                'swal'=>[
                    'text'=>'????????????????????? ?????????? ???????????? ????.',
                    'confirmButtonText'=>'????????',
                    'icon'=>'info'
                ],
                'component'=> $this->generateUrl('app_permissions_list'),
            ]);
        }
        return $this->json(
            [
                'view'=>$this->render('business/permissions/edit.html.twig', [
                    'form' => $form->createView(),
                ]),
                'topView' => $this->render('business/permissions/buttons.html.twig'),

                'title'=>'???????????? ???????????? ???????? : ' . $perm->getUser()->getEmail()

            ]
        );
    }

    #[Route('/app/log/list', name: 'app_log_list' , options: ["expose"=>true])]
    public function app_log_list(\App\Service\permission $permission,Request $request,EntityManagerInterface $entityManager): Response
    {
        if(! $permission->hasPermission('admin',$this->bidObject,$this->getUser()))
            throw $this->createAccessDeniedException();

        return $this->json([
            'view'=> $this->render('business/logs.html.twig', [
                'logs' => $entityManager->getRepository('App:Log')->findBy(['bid'=>$this->bid],['id'=>'DESC']),
            ]),
            'title'=>'?????????????? ?? ???????? ????????????'
        ]);
    }

    #[Route('/app/report/list', name: 'app_report_list' , options: ["expose"=>true])]
    public function app_report_list(\App\Service\permission $permission,Request $request,EntityManagerInterface $entityManager): Response
    {
        if(! $permission->hasPermission('admin',$this->bidObject,$this->getUser()))
            throw $this->createAccessDeniedException();

        return $this->json([
            'view'=> $this->render('business/reports.html.twig', [
                'logs' => $entityManager->getRepository('App:Log')->findBy(['bid'=>$this->bid],['id'=>'DESC']),
            ]),
            'title'=>'??????????????'
        ]);
    }
    #[Route('/app/api', name: 'app_api' , options: ["expose"=>true])]
    public function app_api(\App\Service\permission $permission,Request $request,EntityManagerInterface $entityManager): Response
    {
        if(! $permission->hasPermission('admin',$this->bidObject,$this->getUser()))
            throw $this->createAccessDeniedException();

        return $this->json([
            'view'=> $this->render('business/reports.html.twig', [
                'logs' => $entityManager->getRepository('App:Log')->findBy(['bid'=>$this->bid],['id'=>'DESC']),
            ]),
            'title'=>'???????? ?????????????????????????'
        ]);
    }
}
