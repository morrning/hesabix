<?php

namespace App\Controller;

use App\Entity\BanksAccount;
use App\Entity\BanksTransfer;
use App\Entity\HesabdariItem;
use App\Form\BankTransferEditType;
use App\Form\BankTransferType;
use App\Form\BankType;
use App\Service\hesabdari;
use App\Service\kernel;
use App\Service\Log;
use App\Service\permission;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function PHPUnit\Framework\throwException;

class BankController extends AbstractController
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
        $this->bidObject = $entityManager->getRepository(\App\Entity\Business::class)->find($this->bid);
        if (! $this->bidObject)
            throw $this->createNotFoundException();
        $this->activeYear = $kernel->checkActiveYear($this->request);
        if(!$this->activeYear){
            throw $this->createNotFoundException();
        }
        $this->activeYearObject = $entityManager->getRepository('App:Year')->find($this->activeYear);
    }

    #[Route('/app/bank/list', name: 'app_bank_list', options: ["expose"=>true])]
    public function app_bank_list(permission $permission,Request $request,EntityManagerInterface $entityManager): Response
    {
        if( $permission->hasPermission('bankEdit',$this->bidObject,$this->getUser()) ||  $permission->hasPermission('bankDelete',$this->bidObject,$this->getUser()) )
        {
            return $this->json(
                [
                    'view'=>$this->render('app_main/bank/list.html.twig', [
                        'banks' => $entityManager->getRepository(\App\Entity\BanksAccount::class)->getListAll($this->bid),
                        'bid'=> $this->bid
                    ]),
                    'topView' => $this->render('app_main/bank/topButtons/buttons.html.twig'),
                    'title'=>'حساب‌های بانکی'
                ]
            );
        }

    }

    #[Route('/app/bank/transactions/list/{id}', name: 'app_bank_transactions_list', options: ["expose"=>true])]
    public function app_bank_transactions_list($id, permission $permission,Request $request,EntityManagerInterface $entityManager, hesabdari $hesabdari): Response
    {
        if( $permission->hasPermission('bankPrint',$this->bidObject,$this->getUser()) )
        {
            $bank = $entityManager->getRepository(\App\Entity\BanksAccount::class)->find($id);
            if(! $bank)
                throw $this->createNotFoundException();
            if($bank->getBussiness() != $this->bidObject)
                throw $this->createAccessDeniedException();

            return $this->json(
                [
                    'view'=>$this->render('app_main/bank/transactions_list.html.twig', [
                        'trs' => $hesabdari->getTransactionsByRef('bank:' . $this->bid . ':' . $id),
                        'bank' => $bank,
                        'bid'=> $this->bid
                    ]),
                    'topView' => $this->render('app_main/bank/topButtons/buttons.html.twig'),
                    'title'=>'تراکنش‌های بانک : ' . $bank->getName()
                ]
            );
        }

    }


    #[Route('/app/bank/new', name: 'app_bank_new', options: ["expose"=>true])]
    public function app_bank_new(Log $log,permission $permission,Request $request,EntityManagerInterface $entityManager): Response
    {
        if(! $permission->hasPermission('bankAdd',$this->bidObject,$this->getUser()))
            throw $this->createAccessDeniedException();
        $bank = new BanksAccount();
        $form = $this->createForm(BankType::class,$bank,[
            'action'=>$this->generateUrl('app_bank_new')
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $bank->setArzType($this->bidObject->getArzMain());
            $bank->setBussiness($this->bidObject);
            $entityManager->persist($bank);
            $entityManager->flush();
            $log->add($this->bidObject,$this->getUser(),'web','بانک','افزودن حساب بانکی');

            $response['result'] = 1;
            $response['swal'] = [
                'text'=>'با موفقیت ثبت شد.',
                'confirmButtonText'=>'قبول',
                'icon'=>'success'
            ];
            $response['component'] = $this->generateUrl('app_bank_list');
            return $this->json($response);
        }
        return $this->json(
            [
                'view'=>$this->render('app_main/bank/new.html.twig', [
                    'form' => $form->createView(),
                ]),
                'topView' => $this->render('app_main/bank/topButtons/buttons.html.twig'),

                'title'=>'حساب بانکی جدید'

            ]
        );
    }

    #[Route('/app/bank/edit/{id}', name: 'app_bank_edit', options: ["expose"=>true])]
    public function app_bank_edit($id,Log $log,permission $permission,Request $request,EntityManagerInterface $entityManager): Response
    {
        if(! $permission->hasPermission('bankEdit',$this->bidObject,$this->getUser()))
            throw $this->createAccessDeniedException();
        $bank = $entityManager->getRepository(\App\Entity\BanksAccount::class)->find($id);
        if(!$bank)
            throw $this->createNotFoundException();

        $form = $this->createForm(BankType::class,$bank,[
            'action'=>$this->generateUrl('app_bank_edit',['id'=>$id])
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($bank);
            $entityManager->flush();
            $log->add($this->bidObject,$this->getUser(),'web','بانک','ویرایش حساب بانکی');

            $response['result'] = 1;
            $response['swal'] = [
                'text'=>'با موفقیت ثبت شد.',
                'confirmButtonText'=>'قبول',
                'icon'=>'success'
            ];
            $response['component'] = $this->generateUrl('app_bank_list');
            return $this->json($response);
        }
        return $this->json(
            [
                'view'=>$this->render('app_main/bank/new.html.twig', [
                    'form' => $form->createView(),
                ]),
                'topView' => $this->render('app_main/bank/topButtons/buttons.html.twig'),

                'title'=>'ویرایش حساب بانکی:' . $bank->getName()

            ]
        );
    }
    #[Route('/app/bank/delete/{id}', name: 'app_bank_delete', options: ["expose"=>true])]
    public function app_bank_delete($id,Log $log, permission $permission,Request $request, EntityManagerInterface $entityManager,kernel $kernel): Response
    {
        if(! $permission->hasPermission('bankDelete',$this->bidObject,$this->getUser()))
            throw $this->createAccessDeniedException();
        $bank = $entityManager->getRepository(\App\Entity\BanksAccount::class)->find($id);
        if($bank){
            if($this->bidObject->getOwner() == $this->getUser()){
                $canDelete = true;
                
                if($canDelete){
                    $entityManager->remove($bank);
                    $entityManager->flush();
                    $log->add($this->bidObject,$this->getUser(),'web','بانک','حذف حساب بانکی');
                }
            }
            else{
                return $this->json(
                    [
                        'result'=>0
                    ]
                );
            }
        }
        return $this->json(
            [
                'result'=>1
            ]
        );
    }


    #[Route('/app/bank/transfer/new', name: 'app_bank_transfer_new', options: ["expose"=>true])]
    public function app_bank_transfer_new(kernel $kernel,Log $log,hesabdari $hesabdari, permission $permission,Request $request,EntityManagerInterface $entityManager): Response
    {
        if(! $permission->hasPermission('bankTransferAdd',$this->bidObject,$this->getUser()))
            throw $this->createAccessDeniedException();
        $year = $kernel->checkActiveYear($request);
        if($year){
            if(!$year->getActive()){
                return $kernel->msgNotActiveYear();
            }
        }
        $transfer = new BanksTransfer();
        $form = $this->createForm(BankTransferType::class,$transfer,[
            'action'=>$this->generateUrl('app_bank_transfer_new'),
            'bid'=>$this->bid
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $transfer->setBid($this->bidObject);
            $transfer->setSubmitter($this->getUser());
            $transfer->setYear($year);
            $entityManager->getRepository('App:BanksTransfer')->add($transfer);

            //add hesabdari file
            $fitem = new HesabdariItem();
            $fitem->setType('bank');
            $fitem->setTypeData($transfer->getSideOne()->getId());
            $fitem->setDes('انتقال بین بانکی: ' . $transfer->getSideOne()->getName());
            $fitem->setBs($transfer->getAmount());
            $fitem->setBd(0);
            $fitem->setCode($entityManager->getRepository('App:HesabdariTable')->findOneBy(['code'=>10001]));

            $sitem = new HesabdariItem();
            $sitem->setType('bank');
            $sitem->setTypeData($transfer->getSideTwo()->getId());
            $sitem->setDes('انتقال بین بانکی: ' . $transfer->getSideTwo()->getName());
            $sitem->setBd($transfer->getAmount());
            $sitem->setBs(0);
            $sitem->setCode($entityManager->getRepository('App:HesabdariTable')->findOneBy(['code'=>10001]));

            $ref = 'banktransfer:' . $this->bid . ':' . $transfer->getId();
            $des = 'انتقال بین حساب بانکی';

            $hesabdari->insertNewDoc($this->getUser(),$transfer->getDateSave(),$des,$this->bidObject,$ref,[$fitem,$sitem]);
            $log->add($this->bidObject,$this->getUser(),'web','بانک','افزودن انتقال بین حساب بانکی');

            $response['result'] = 1;
            $response['swal'] = [
                'text'=>'با موفقیت ثبت شد.',
                'confirmButtonText'=>'قبول',
                'icon'=>'success'
            ];
            $response['component'] = $this->generateUrl('app_bank_transfer_list');
            return $this->json($response);
        }
        return $this->json(
            [
                'view'=>$this->render('app_main/bank/transfer/new.html.twig', [
                    'form' => $form->createView(),
                ]),
                'topView' => $this->render('app_main/bank/topButtons/buttons.html.twig'),

                'title'=>'انتقال بین حساب بانکی'

            ]
        );
    }

    #[Route('/app/bank/transfer/edit/{id}', name: 'app_bank_transfer_edit', options: ["expose"=>true])]
    public function app_bank_transfer_edit($id,kernel $kernel, Log $log,hesabdari $hesabdari, permission $permission, Request $request,EntityManagerInterface $entityManager): Response
    {
        if(! $permission->hasPermission('bankTransferEdit',$this->bidObject,$this->getUser()))
            throw $this->createAccessDeniedException();
        $year = $kernel->checkActiveYear($request);
        if($year){
            if(!$year->getActive()){
                return $kernel->msgNotActiveYear();
            }
        }
        $transfer = $entityManager->getRepository('App:BanksTransfer')->find($id);
        if(! $transfer)
            throw $this->createNotFoundException();
        $form = $this->createForm(BankTransferEditType::class,$transfer,[
            'action'=>$this->generateUrl('app_bank_transfer_edit',['id'=>$id]),
            'bid'=>$this->bid
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $transfer->setBid($this->bidObject);
            $transfer->setSubmitter($this->getUser());
            $entityManager->persist($transfer);
            $entityManager->flush();
            //remove old hesabdari doc
            $hesabdari->removeByRef('banktransfer',$this->bid,$transfer->getId());

            //add hesabdari file
            $fitem = new HesabdariItem();
            $fitem->setType('bank');
            $fitem->setTypeData($transfer->getSideOne()->getId());
            $fitem->setDes('حساب بانکی: ' . $transfer->getSideOne()->getName());
            $fitem->setBs($transfer->getAmount());
            $fitem->setBd(0);
            $fitem->setCode($entityManager->getRepository('App:HesabdariTable')->findOneBy(['code'=>1002]));

            $sitem = new HesabdariItem();
            $sitem->setType('bank');
            $sitem->setTypeData($transfer->getSideTwo()->getId());
            $sitem->setDes('حساب بانکی: ' . $transfer->getSideTwo()->getName());
            $sitem->setBd($transfer->getAmount());
            $sitem->setBs(0);
            $sitem->setCode($entityManager->getRepository('App:HesabdariTable')->findOneBy(['code'=>1002]));

            $ref = 'banktransfer:' . $this->bid . ':' . $transfer->getId();
            $des = 'انتقال بین حساب بانکی';

            $hesabdari->insertNewDoc($this->getUser(),$transfer->getDateSave(),$des,$this->bidObject,$ref,[$fitem,$sitem]);
            $log->add($this->bidObject,$this->getUser(),'web','بانک','ویرایش انتقال بین حساب بانکی');

            $response['swal'] = [
                'text'=>'با موفقیت ثبت شد.',
                'confirmButtonText'=>'قبول',
                'icon'=>'success'
            ];
            $response['component'] = $this->generateUrl('app_bank_transfer_list');
            return $this->json($response);
        }
        return $this->json(
            [
                'view'=>$this->render('app_main/bank/transfer/new.html.twig', [
                    'form' => $form->createView(),
                ]),
                'topView' => $this->render('app_main/bank/topButtons/buttons.html.twig'),

                'title'=>'ویرایش انتقال بین حساب بانکی'

            ]
        );
    }

    #[Route('/app/bank/transfer/list', name: 'app_bank_transfer_list', options: ["expose"=>true])]
    public function app_bank_transfer_list(permission $permission,Request $request,EntityManagerInterface $entityManager): Response
    {
        if( $permission->hasPermission('bankTransferEdit',$this->bidObject,$this->getUser()) ||  $permission->hasPermission('bankTransferDelete',$this->bidObject,$this->getUser()) )
        {
            return $this->json(
                [
                    'view'=>$this->render('app_main/bank/transfer/list.html.twig', [
                        'datas' => $entityManager->getRepository('App:BanksTransfer')->getListAll($this->bid,$this->activeYear)
                    ]),
                    'topView' => $this->render('app_main/bank/topButtons/buttons.html.twig'),
                    'title'=>'لیست انتقال‌های بین بانکی'
                ]
            );
        }
        throw $this->createAccessDeniedException();

    }

    #[Route('/app/bank/transfer/delete/{id}', name: 'app_bank_transfer_delete', options: ["expose"=>true])]
    public function app_bank_transfer_delete($id,Log $log,hesabdari $hesabdari,permission $permission, Request $request, EntityManagerInterface $entityManager,kernel $kernel): Response
    {
        if(! $permission->hasPermission('bankTransferDelete',$this->bidObject,$this->getUser()))
            throw $this->createAccessDeniedException();
        $transfer = $entityManager->getRepository('App:BanksTransfer')->find($id);
        if($transfer){
            $hesabdari->removeByRef('banktransfer',$this->bid,$transfer->getId());
            $entityManager->getRepository('App:BanksTransfer')->remove($transfer);
            $log->add($this->bidObject,$this->getUser(),'web','بانک','حذف انتقال بین حساب بانکی');

        }
        return $this->json(
            [
                'result'=>1
            ]
        );
    }
}
