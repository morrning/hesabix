<?php

namespace App\Controller;

use App\Entity\Cost;
use App\Entity\HesabdariItem;
use App\Entity\IncomeFile;
use App\Form\CostType;
use App\Form\IncomeType;
use App\Service\hesabdari;
use App\Service\kernel;
use App\Service\Log;
use App\Service\pdfMGR;
use App\Service\permission;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CostController extends AbstractController
{
    private $bid = null;
    private $request = null;
    private $bidObject = null;
    private $activeYear = null;
    private $activeYearObject = null;
    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager, kernel $kernel)
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

    /**
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\ORMException
     */
    #[Route('/app/cost/new', name: 'app_cost_new', options: ["expose"=>true])]
    public function app_cost_new(Log $log,permission $permission,Request $request,EntityManagerInterface$entityManager,hesabdari $hesabdari): Response
    {
        if(! $permission->hasPermission('castAdd',$this->bidObject,$this->getUser()))
            throw $this->createAccessDeniedException();
        $cost = new Cost();
        $form = $this->createForm(CostType::class,$cost,[
            'action'=>$this->generateUrl('app_cost_new'),
            'bid'=>$this->bid
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if($cost->getAmount()<=0){
                $response['result'] = 0;
                $response['modal-stay'] = 1;
                $response['swal'] = [
                    'text'=>'???????? ???????? ?????? ???? ?????????? ??????',
                    'confirmButtonText'=>'????????',
                    'icon'=>'error'
                ];
                return $this->json($response);
            }
            $cost->setBid($this->bidObject);
            $cost->setSubmitter($this->getUser());
            $cost->setDateSubmit(time());
            $cost->setYear($this->activeYearObject);
            $entityManager->getRepository('App:Cost')->add($cost);

            //add hesabdari file
            $fitem = new HesabdariItem();
            $fitem->setType('bank');
            $fitem->setTypeData($cost->getBank()->getId());
            $fitem->setDes('???????? ??????????: ' . $cost->getBank()->getName() . ' ??????: ' . $cost->getDes());
            $fitem->setBs($cost->getAmount());
            $fitem->setBd(0);
            $fitem->setCode($entityManager->getRepository('App:HesabdariTable')->findOneBy(['code'=>10001]));

            $sitem = new HesabdariItem();
            $sitem->setType('cost');
            $sitem->setTypeData($cost->getId());
            $sitem->setDes('??????????: ' .$cost->getHesabdariTable()->getName() . ' ??????: ' . $cost->getDes());
            $sitem->setBd($cost->getAmount());
            $sitem->setBs(0);
            $sitem->setCode($entityManager->getRepository('App:HesabdariTable')->findOneBy(['code'=>$cost->getHesabdariTable()->getCode()]));

            $ref = 'cost:' . $this->bid . ':' . $cost->getId();
            $des = '??????????: ' . $cost->getDes();

            $hesabdari->insertNewDoc($this->getUser(),$cost->getDateSave(),$des,$this->bidObject,$ref,[$fitem,$sitem]);
            $log->add($this->bidObject,$this->getUser(),'web','???????? ?? ??????????','???????????? ??????????' . ' ??????: ' . $cost->getDes());

            $response['result'] = 1;
            $response['swal'] = [
                'text'=>'???? ???????????? ?????? ????.',
                'confirmButtonText'=>'????????',
                'icon'=>'success'
            ];
            $response['component'] = $this->generateUrl('app_cost_list');
            return $this->json($response);
        }
        return $this->json(
            [
                'view'=>$this->render('app_main/cost/new.html.twig', [
                    'form' => $form->createView(),
                ]),
                'topView' => $this->render('app_main/cost/buttons.html.twig'),
                'title'=>'?????????? ????????'
            ]
        );
    }

    #[Route('/app/cost/edit/{id}', name: 'app_cost_edit', options: ["expose"=>true])]
    public function app_cost_edit($id, Log $log,permission $permission,Request $request,EntityManagerInterface$entityManager,hesabdari $hesabdari): Response
    {
        if(! $permission->hasPermission('castEdit',$this->bidObject,$this->getUser()))
            throw $this->createAccessDeniedException();
        $cost = $entityManager->getRepository('App:Cost')->find($id);
        if(!$cost)
            throw $this->createNotFoundException();

        $form = $this->createForm(CostType::class,$cost,[
            'action'=>$this->generateUrl('app_cost_edit',['id'=>$id]),
            'bid'=>$this->bid
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $cost->setSubmitter($this->getUser());
            $cost->setDateSubmit(time());
            $entityManager->getRepository('App:Cost')->add($cost);
            //remove old hesabdari file
            $hesabdari->removeByRef('cost',$this->bid,$cost->getId());

            //add hesabdari file
            $fitem = new HesabdariItem();
            $fitem->setType('bank');
            $fitem->setTypeData($cost->getBank()->getId());
            $fitem->setDes('???????? ??????????: ' . $cost->getBank()->getName(). ' ??????: ' . $cost->getDes());
            $fitem->setBs($cost->getAmount());
            $fitem->setBd(0);
            $fitem->setCode($entityManager->getRepository('App:HesabdariTable')->findOneBy(['code'=>1002]));

            $sitem = new HesabdariItem();
            $sitem->setType('cost');
            $sitem->setTypeData($cost->getId());
            $sitem->setDes('??????????: ' .$cost->getHesabdariTable()->getName(). ' ??????: ' . $cost->getDes());
            $sitem->setBd($cost->getAmount());
            $sitem->setBs(0);
            $sitem->setCode($entityManager->getRepository('App:HesabdariTable')->findOneBy(['code'=>$cost->getHesabdariTable()->getCode()]));

            $ref = 'cost:' . $this->bid . ':' . $cost->getId();
            $des = '??????????: ' . $cost->getDes();

            $hesabdari->insertNewDoc($this->getUser(),$cost->getDateSave(),$des,$this->bidObject,$ref,[$fitem,$sitem]);
            $log->add($this->bidObject,$this->getUser(),'web','????????  ?? ??????????','???????????? ??????????'. ' ??????: ' . $cost->getDes());

            $response['result'] = 1;
            $response['swal'] = [
                'text'=>'???? ???????????? ?????? ????.',
                'confirmButtonText'=>'????????',
                'icon'=>'success'
            ];
            $response['component'] = $this->generateUrl('app_cost_list');
            return $this->json($response);
        }
        return $this->json(
            [
                'view'=>$this->render('app_main/cost/new.html.twig', [
                    'form' => $form->createView(),
                ]),
                'topView' => $this->render('app_main/cost/buttons.html.twig'),
                'title'=>'???????????? ??????????'

            ]
        );
    }

    #[Route('/app/cost/list', name: 'app_cost_list', options: ["expose"=>true])]
    public function app_cost_list(permission $permission,Request $request,EntityManagerInterface $entityManager): Response
    {
        if( $permission->hasPermission('castEdit',$this->bidObject,$this->getUser()) ||  $permission->hasPermission('castDelete',$this->bidObject,$this->getUser()) )
        {
            return $this->json(
                [
                    'view'=>$this->render('app_main/cost/list.html.twig', [
                        'datas' => $entityManager->getRepository('App:Cost')->getListAll($this->bid,$this->activeYear)
                    ]),
                    'topView' => $this->render('app_main/cost/buttons.html.twig'),
                    'title'=>'???????? ?????????????????'
                ]
            );
        }
        throw $this->createAccessDeniedException();
    }

    #[Route('/app/cost/delete/{id}', name: 'app_cost_delete', options: ["expose"=>true])]
    public function app_cost_delete($id,Log $log,hesabdari $hesabdari,permission $permission, Request $request, EntityManagerInterface $entityManager,kernel $kernel): Response
    {
        if(! $permission->hasPermission('castDelete',$this->bidObject,$this->getUser()))
            throw $this->createAccessDeniedException();
        $cost = $entityManager->getRepository('App:Cost')->find($id);
        if($cost){
            $hesabdari->removeByRef('cost',$this->bid,$cost->getId());
            $entityManager->getRepository('App:Cost')->remove($cost);
            $log->add($this->bidObject,$this->getUser(),'web','???????? ?? ??????????','?????? ??????????');

        }
        return $this->json(
            [
                'result'=>1
            ]
        );
    }

    #[Route('/app/cost/list/print', name: 'app_cost_list_print', options: ["expose"=>true])]
    public function app_cost_list_print(pdfMGR $pdfMGR, permission $permission,Request $request, EntityManagerInterface $entityManager,kernel $kernel): Response
    {
        if(! $permission->hasPermission('castPrint',$this->bidObject,$this->getUser()))
            throw $this->createAccessDeniedException();
        $pdfMGR->streamTwig2PDF('app_main/cost/list_pdf.html.twig',[
            'datas' => $entityManager->getRepository('App:Cost')->getListAll($this->bid,$this->activeYear),
            'bid'=>$this->bidObject,
            'year'=>$this->activeYearObject,
            'page_title'=>'???????? ?????????????????'
        ]);
    }
}
