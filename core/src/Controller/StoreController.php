<?php

namespace App\Controller;

use App\Entity\BanksAccount;
use App\Entity\Store;
use App\Form\BankType;
use App\Form\StoreType;
use App\Service\kernel;
use App\Service\Log;
use App\Service\permission;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StoreController extends AbstractController
{
    private $bid = null;
    private $request = null;
    private $bidObject = null;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager, kernel $kernel)
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->bid = $kernel->checkBID($this->request);
        if(!$this->bid){
            throw $this->createNotFoundException();
        }
        $this->bidObject = $entityManager->getRepository(\App\Entity\Business::class)->find($this->bid);
        if (! $this->bidObject)
            throw $this->createNotFoundException();
    }

    #[Route('/app/store/list', name: 'app_store_list', options: ["expose"=>true])]
    public function app_store_list(permission $permission,Request $request,EntityManagerInterface $entityManager): Response
    {
        if( $permission->hasPermission('storeEdit',$this->bidObject,$this->getUser()) ||  $permission->hasPermission('storeDelete',$this->bidObject,$this->getUser()) )
        {
            return $this->json(
                [
                    'view'=>$this->render('app_main/store/list.html.twig', [
                        'datas' => $entityManager->getRepository('App:Store')->findBy(['bid'=>$this->bid]),
                        'bid'=> $this->bid
                    ]),
                    'topView' => $this->render('app_main/store/topButtons/buttons.html.twig'),
                    'title'=>'انبارها'
                ]
            );
        }

    }

    #[Route('/app/store/new', name: 'app_store_new', options: ["expose"=>true])]
    public function app_store_new(Log $log,permission $permission,EntityManagerInterface $entityManager,Request $request): Response
    {
        if(! $permission->hasPermission('bankAdd',$this->bidObject,$this->getUser()))
            throw $this->createAccessDeniedException();
        $store = new Store();
        $form = $this->createForm(StoreType::class,$store,[
            'action'=>$this->generateUrl('app_store_new')
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //check main store exist before
            $exist = $entityManager->getRepository('App:Store')->findOneBy(['bid'=>$this->bid,'main'=>true]);
            if(!$exist)
                $store->setMain(true);

            $store->setSubmitter($this->getUser());
            $store->setBid($this->bidObject);
            $entityManager->persist($store);
            $entityManager->flush();
            $log->add($this->bidObject,$this->getUser(),'web','انبارداری','افزودن انبار');

            $response['result'] = 1;
            $response['swal'] = [
                'text'=>'با موفقیت ثبت شد.',
                'confirmButtonText'=>'قبول',
                'icon'=>'success'
            ];
            $response['component'] = $this->generateUrl('app_store_list');
            return $this->json($response);
        }
        return $this->json(
            [
                'view'=>$this->render('app_main/store/new.html.twig', [
                    'form' => $form->createView(),
                ]),
                'topView' => $this->render('app_main/store/topButtons/buttons.html.twig'),

                'title'=>'انبار جدید'

            ]
        );
    }
    #[Route('/app/store/edit/{id}', name: 'app_store_edit', options: ["expose"=>true])]
    public function app_store_edit(Log $log,permission $permission,EntityManagerInterface $entityManager,Request $request,$id): Response
    {
        if(! $permission->hasPermission('bankEdit',$this->bidObject,$this->getUser()))
            throw $this->createAccessDeniedException();
        $store = $entityManager->getRepository('App:Store')->find($id);
        if(!$store)
            throw $this->createNotFoundException();
        if($store->getBid() != $this->bidObject )
            throw $this->createAccessDeniedException();
        $form = $this->createForm(StoreType::class,$store,[
            'action'=>$this->generateUrl('app_store_edit',['id'=>$id])
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($store);
            $entityManager->flush();
            $log->add($this->bidObject,$this->getUser(),'web','انبارداری','ویرایش انبار');

            $response['result'] = 1;
            $response['swal'] = [
                'text'=>'با موفقیت ثبت شد.',
                'confirmButtonText'=>'قبول',
                'icon'=>'success'
            ];
            $response['component'] = $this->generateUrl('app_store_list');
            return $this->json($response);
        }
        return $this->json(
            [
                'view'=>$this->render('app_main/store/new.html.twig', [
                    'form' => $form->createView(),
                ]),
                'topView' => $this->render('app_main/store/topButtons/buttons.html.twig'),

                'title'=>'انبار جدید'

            ]
        );
    }
}
