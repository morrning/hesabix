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
                    'text'=>'مبلغ وارد شده نا معتبر است',
                    'confirmButtonText'=>'قبول',
                    'icon'=>'error'
                ];
                return $this->json($response);
            }
            $cost->setBid($this->bidObject);
            $cost->setSubmitter($this->getUser());
            $cost->setDateSubmit(time());
            $entityManager->getRepository('App:Cost')->add($cost);

            //add hesabdari file
            $fitem = new HesabdariItem();
            $fitem->setType('bank');
            $fitem->setTypeData($cost->getBank()->getId());
            $fitem->setDes('حساب بانکی: ' . $cost->getBank()->getName() . ' شرح: ' . $cost->getDes());
            $fitem->setBs($cost->getAmount());
            $fitem->setBd(0);
            $fitem->setCode($entityManager->getRepository('App:HesabdariTable')->findOneBy(['code'=>10001]));

            $sitem = new HesabdariItem();
            $sitem->setType('cost');
            $sitem->setTypeData($cost->getId());
            $sitem->setDes('هزینه: ' .$cost->getHesabdariTable()->getName() . ' شرح: ' . $cost->getDes());
            $sitem->setBd($cost->getAmount());
            $sitem->setBs(0);
            $sitem->setCode($entityManager->getRepository('App:HesabdariTable')->findOneBy(['code'=>$cost->getHesabdariTable()->getCode()]));

            $ref = 'cost:' . $this->bid . ':' . $cost->getId();
            $des = 'هزینه: ' . $cost->getDes();

            $hesabdari->insertNewDoc($this->getUser(),$cost->getDateSave(),$des,$this->bidObject,$ref,[$fitem,$sitem]);
            $log->add($this->bidObject,$this->getUser(),'web','خرید و هزینه','افزودن هزینه' . ' شرح: ' . $cost->getDes());

            $response['result'] = 1;
            $response['swal'] = [
                'text'=>'با موفقیت ثبت شد.',
                'confirmButtonText'=>'قبول',
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

                'title'=>'هزینه جدید'

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
            $fitem->setDes('حساب بانکی: ' . $cost->getBank()->getName(). ' شرح: ' . $cost->getDes());
            $fitem->setBs($cost->getAmount());
            $fitem->setBd(0);
            $fitem->setCode($entityManager->getRepository('App:HesabdariTable')->findOneBy(['code'=>1002]));

            $sitem = new HesabdariItem();
            $sitem->setType('cost');
            $sitem->setTypeData($cost->getId());
            $sitem->setDes('هزینه: ' .$cost->getHesabdariTable()->getName(). ' شرح: ' . $cost->getDes());
            $sitem->setBd($cost->getAmount());
            $sitem->setBs(0);
            $sitem->setCode($entityManager->getRepository('App:HesabdariTable')->findOneBy(['code'=>$cost->getHesabdariTable()->getCode()]));

            $ref = 'cost:' . $this->bid . ':' . $cost->getId();
            $des = 'هزینه: ' . $cost->getDes();

            $hesabdari->insertNewDoc($this->getUser(),$cost->getDateSave(),$des,$this->bidObject,$ref,[$fitem,$sitem]);
            $log->add($this->bidObject,$this->getUser(),'web','خرید  و هزینه','ویرایش هزینه'. ' شرح: ' . $cost->getDes());

            $response['result'] = 1;
            $response['swal'] = [
                'text'=>'با موفقیت ثبت شد.',
                'confirmButtonText'=>'قبول',
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
                'title'=>'ویرایش هزینه'

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
                        'datas' => $entityManager->getRepository('App:Cost')->getListAll($this->bid)
                    ]),
                    'topView' => $this->render('app_main/cost/buttons.html.twig'),
                    'title'=>'لیست هزینه‌ها'
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
            $log->add($this->bidObject,$this->getUser(),'web','خرید و هزینه','حذف هزینه');

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
            'datas' => $entityManager->getRepository('App:Cost')->getListAll($this->bid),
            'bid'=>$this->bidObject,
            'page_title'=>'لیست هزینه‌ها'
        ]);
    }
}
