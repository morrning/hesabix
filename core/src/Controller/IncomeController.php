<?php

namespace App\Controller;

use App\Entity\BanksTransfer;
use App\Entity\HesabdariItem;
use App\Entity\IncomeFile;
use App\Form\BankTransferType;
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

class IncomeController extends AbstractController
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

    #[Route('/app/income/new', name: 'app_income_new', options: ["expose"=>true])]
    public function app_income_new(Log $log,permission $permission,Request $request,EntityManagerInterface$entityManager,hesabdari $hesabdari): Response
    {
        if(! $permission->hasPermission('incomeAdd',$this->bidObject,$this->getUser()))
            throw $this->createAccessDeniedException();
        $income = new IncomeFile();
        $form = $this->createForm(IncomeType::class,$income,[
            'action'=>$this->generateUrl('app_income_new'),
            'bid'=>$this->bid
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if($income->getAmount()<=0){
                $response['result'] = 0;
                $response['modal-stay'] = 1;
                $response['swal'] = [
                    'text'=>'مبلغ وارد شده نا معتبر است',
                    'confirmButtonText'=>'قبول',
                    'icon'=>'error'
                ];
                return $this->json($response);
            }
            $income->setBid($this->bidObject);
            $income->setUser($this->getUser());
            $income->setDateSubmit(time());
            $entityManager->getRepository('App:IncomeFile')->add($income);

            //add hesabdari file
            $fitem = new HesabdariItem();
            $fitem->setType('bank');
            $fitem->setTypeData($income->getBank()->getId());
            $fitem->setDes('حساب بانکی: ' . $income->getBank()->getName(). ' شرح: ' . $income->getDes());
            $fitem->setBd($income->getAmount());
            $fitem->setBs(0);
            $fitem->setCode($entityManager->getRepository('App:HesabdariTable')->findOneBy(['code'=>1002]));

            $sitem = new HesabdariItem();
            $sitem->setType('income');
            $sitem->setTypeData($income->getId());
            $sitem->setDes('درآمد: ' .$income->getIncomeTable()->getName(). ' شرح: ' . $income->getDes());
            $sitem->setBs($income->getAmount());
            $sitem->setBd(0);
            $sitem->setCode($entityManager->getRepository('App:HesabdariTable')->findOneBy(['code'=>$income->getIncomeTable()->getCode()]));

            $ref = 'income:' . $this->bid . ':' . $income->getId();
            $des = 'درآمد';

            $hesabdari->insertNewDoc($this->getUser(),$income->getDateSave(),$des,$this->bidObject,$ref,[$fitem,$sitem]);
            $log->add($this->bidObject,$this->getUser(),'web','فروش و درآمد','افزودن درآمد'. ' شرح: ' . $income->getDes());

            $response['result'] = 1;
            $response['swal'] = [
                'text'=>'با موفقیت ثبت شد.',
                'confirmButtonText'=>'قبول',
                'icon'=>'success'
            ];
            $response['component'] = $this->generateUrl('app_income_list');
            return $this->json($response);
        }
        return $this->json(
            [
                'view'=>$this->render('app_main/income/new.html.twig', [
                    'form' => $form->createView(),
                ]),
                'topView' => $this->render('app_main/income/buttons.html.twig'),

                'title'=>'درآمد جدید'

            ]
        );
    }

    #[Route('/app/income/edit/{id}', name: 'app_income_edit', options: ["expose"=>true])]
    public function app_income_edit($id, Log $log,permission $permission,Request $request,EntityManagerInterface$entityManager,hesabdari $hesabdari): Response
    {
        if(! $permission->hasPermission('incomeEdit',$this->bidObject,$this->getUser()))
            throw $this->createAccessDeniedException();
        $income = $entityManager->getRepository('App:IncomeFile')->find($id);
        if(!$income)
            throw $this->createNotFoundException();

        $form = $this->createForm(IncomeType::class,$income,[
            'action'=>$this->generateUrl('app_income_edit',['id'=>$id]),
            'bid'=>$this->bid
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $income->setUser($this->getUser());
            $income->setDateSubmit(time());
            $entityManager->getRepository('App:IncomeFile')->add($income);
            //remove old hesabdari file
            $hesabdari->removeByRef('income',$this->bid,$income->getId());

            //add hesabdari file
            $fitem = new HesabdariItem();
            $fitem->setType('bank');
            $fitem->setTypeData($income->getBank()->getId());
            $fitem->setDes('حساب بانکی: ' . $income->getBank()->getName(). ' شرح: ' . $income->getDes());
            $fitem->setBd($income->getAmount());
            $fitem->setBs(0);
            $fitem->setCode($entityManager->getRepository('App:HesabdariTable')->findOneBy(['code'=>1002]));

            $sitem = new HesabdariItem();
            $sitem->setType('income');
            $sitem->setTypeData($income->getId());
            $sitem->setDes('درآمد: ' .$income->getIncomeTable()->getName(). ' شرح: ' . $income->getDes());
            $sitem->setBs($income->getAmount());
            $sitem->setBd(0);
            $sitem->setCode($entityManager->getRepository('App:HesabdariTable')->findOneBy(['code'=>$income->getIncomeTable()->getCode()]));

            $ref = 'income:' . $this->bid . ':' . $income->getId();
            $des = 'درآمد: ' . $income->getDes();

            $hesabdari->insertNewDoc($this->getUser(),$income->getDateSave(),$des,$this->bidObject,$ref,[$fitem,$sitem]);
            $log->add($this->bidObject,$this->getUser(),'web','فروش و درآمد','ویرایش درآمد'. ' شرح: ' . $income->getDes());

            $response['result'] = 1;
            $response['swal'] = [
                'text'=>'با موفقیت ثبت شد.',
                'confirmButtonText'=>'قبول',
                'icon'=>'success'
            ];
            $response['component'] = $this->generateUrl('app_income_list');
            return $this->json($response);
        }
        return $this->json(
            [
                'view'=>$this->render('app_main/income/new.html.twig', [
                    'form' => $form->createView(),
                ]),
                'topView' => $this->render('app_main/income/buttons.html.twig'),

                'title'=>'ویرایش درآمد'

            ]
        );
    }

    #[Route('/app/income/list', name: 'app_income_list', options: ["expose"=>true])]
    public function app_income_list(permission $permission,Request $request,EntityManagerInterface $entityManager): Response
    {
        if( $permission->hasPermission('IncomeEdit',$this->bidObject,$this->getUser()) ||  $permission->hasPermission('IncomeDelete',$this->bidObject,$this->getUser()) )
        {
            return $this->json(
                [
                    'view'=>$this->render('app_main/income/list.html.twig', [
                        'datas' => $entityManager->getRepository('App:IncomeFile')->getListAll($this->bid)
                    ]),
                    'topView' => $this->render('app_main/income/buttons.html.twig'),
                    'title'=>'لیست درآمدها'
                ]
            );
        }
        throw $this->createAccessDeniedException();
    }

    #[Route('/app/income/delete/{id}', name: 'app_income_delete', options: ["expose"=>true])]
    public function app_income_delete($id,Log $log,hesabdari $hesabdari,permission $permission, Request $request, EntityManagerInterface $entityManager,kernel $kernel): Response
    {
        if(! $permission->hasPermission('incomeDelete',$this->bidObject,$this->getUser()))
            throw $this->createAccessDeniedException();
        $income = $entityManager->getRepository('App:IncomeFile')->find($id);
        if($income){
            $hesabdari->removeByRef('income',$this->bid,$income->getId());
            $entityManager->getRepository('App:IncomeFile')->remove($income);
            $log->add($this->bidObject,$this->getUser(),'web','فروش و درآمد','حذف درآمد');

        }
        return $this->json(
            [
                'result'=>1
            ]
        );
    }

    #[Route('/app/income/list/print', name: 'app_income_list_print', options: ["expose"=>true])]
    public function app_income_list_print(pdfMGR $pdfMGR, permission $permission,Request $request, EntityManagerInterface $entityManager,kernel $kernel): Response
    {
        if(! $permission->hasPermission('incomePrint',$this->bidObject,$this->getUser()))
            throw $this->createAccessDeniedException();
        $pdfMGR->streamTwig2PDF('app_main/income/list_pdf.html.twig',[
            'datas' => $entityManager->getRepository('App:IncomeFile')->getListAll($this->bid),
            'bid'=>$this->bidObject,
            'page_title'=>'لیست درآمدها'
        ]);
    }
}
