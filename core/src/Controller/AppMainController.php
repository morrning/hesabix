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

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager, \App\Service\kernel $kernel)
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->bid = $kernel->checkBID($this->request);
        if($this->bid){
            $this->bidObject = $entityManager->getRepository('App:Business')->find($this->bid);
        }
    }

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
            $log->add($this->bidObject,$this->getUser(),'web','پیکربندی','ویرایش کسب و کار');

            $response['result'] = 1;
            $response['swal'] = [
                'text'=>'با موفقیت ویرایش شد.',
                'confirmButtonText'=>'قبول',
                'icon'=>'success'
            ];
            return $this->json($response);
        }

        return $this->json([
            'view'=>$this->render('business/new.html.twig', [
                'form' => $form->createView(),
            ]),
            'title'=>'ویرایش اطلاعات کسب و کار'
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
            'text'=>'سال مالی با تغییر یافت',
            'confirmButtonText'=>'قبول',
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
        return $this->json(
            [
                'view'=>$this->render('app_main/dashboard.html.twig', [
                    'persons' => $entityManager->getRepository('App:Person')->findBy(['bid'=>$bid]),
                    'banks' => $entityManager->getRepository('App:BanksAccount')->findBy(['bussiness'=>$bid])
                ]),
                'title'=>'داشبورد'
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
            'title'=>'کاربران و سطوح دسترسی'
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
                    'text'=>'کاربری با ایمیل وارد شده یافت نشد.',
                    'confirmButtonText'=>'قبول',
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
           $log->add($this->bidObject,$this->getUser(),'web','پیکربندی','افزودن دسترسی کاربر با ایمیل: ' . $perm->getUser()->getEmail());

           return $this->json([
               'swal'=>[
                   'text'=>'کاربر با موفقیت افزوده شد',
                   'confirmButtonText'=>'قبول',
                   'icon'=>'success'
               ],
               'component'=> $this->generateUrl('app_permissions_list')
           ]);
       }
        return $this->json([
            'swal'=>[
                'text'=>'کاربر قبلا افزوده شده است.',
                'confirmButtonText'=>'قبول',
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
        $log->add($this->bidObject,$this->getUser(),'web','پیکربندی','حذف دسترسی کاربر با ایمیل: ' . $perm->getUser()->getEmail());

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
            $log->add($this->bidObject,$this->getUser(),'web','پیکربندی','ویرایش دسترسی کاربر با ایمیل: ' . $perm->getUser()->getEmail());
            return $this->json([
                'swal'=>[
                    'text'=>'دسترسی‌های کاربر ویرایش شد.',
                    'confirmButtonText'=>'قبول',
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

                'title'=>'ویرایش دسترسی برای : ' . $perm->getUser()->getEmail()

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
            'title'=>'کاربران و سطوح دسترسی'
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
            'title'=>'گزارشات'
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
            'title'=>'رابط برنامه‌نویسی'
        ]);
    }
}
