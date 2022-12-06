<?php

namespace App\Controller;

use App\Entity\BanksAccount;
use App\Entity\Hbuy;
use App\Entity\HbuyItem;
use App\Entity\HbuyPay;
use App\Form\BankType;
use App\Form\HbuyItemType;
use App\Form\HbuyPayType;
use App\Form\HbuyType;
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

class HbuyController extends AbstractController
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

    #[Route('/app/hbuy/list', name: 'app_hbuy_list', options: ["expose"=>true])]
    public function app_hbuy_list(permission $permission,Request $request,EntityManagerInterface $entityManager): Response
    {
        if( $permission->hasPermission('buyEdit',$this->bidObject,$this->getUser()) ||  $permission->hasPermission('buyDelete',$this->bidObject,$this->getUser()) )
        {
            return $this->json(
                [
                    'view'=>$this->render('hbuy/list.html.twig', [
                        'datas' => $entityManager->getRepository('App:hbuy')->getListAll($this->bid),
                        'bid'=> $this->bid
                    ]),
                    'topView' => $this->render('hbuy/buttons.html.twig'),
                    'title'=>'فاکتورهای خرید'
                ]
            );
        }
    }
    #[Route('/app/hbuy/print/{id}', name: 'app_hbuy_print', options: ["expose"=>true])]
    public function app_hbuy_print($id,Log $log,permission $permission,Request $request,EntityManagerInterface $entityManager,pdfMGR $pdfMGR): Response
    {
        if (!$permission->hasPermission('personAdd', $this->bidObject, $this->getUser()))
            throw $this->createAccessDeniedException();
        $item = $entityManager->getRepository('App:Hbuy')->find($id);
        if(!$item)
            throw $this->createNotFoundException();

        $pdfMGR->streamTwig2PDF('hbuy/print.html.twig',[
            'item'=>$item,
            'datas' => $entityManager->getRepository('App:hbuyItem')->findBy(['hbuy'=>$item]),
            'bid'=>$this->bidObject,
            'page_title'=>'فاکتور خرید'
        ]);
    }
    #[Route('/app/hbuy/delete/{id}', name: 'app_hbuy_delete', options: ["expose"=>true])]
    public function app_hbuy_delete($id,Log $log, permission $permission,Request $request, EntityManagerInterface $entityManager,kernel $kernel): Response
    {
        if(! $permission->hasPermission('hbuyDelete',$this->bidObject,$this->getUser()))
            throw $this->createAccessDeniedException();
        $data = $entityManager->getRepository('App:Hbuy')->find($id);
        if($data){
            if($this->bidObject->getOwner() == $this->getUser()){
                $canDelete = true;

                if($canDelete){
                    $entityManager->remove($data);
                    $entityManager->flush();
                    $log->add($this->bidObject,$this->getUser(),'web','خرید و هزینه','حذف فاکتور خرید');
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
    #[Route('/app/hbuy/item/new/row', name: 'app_hboy_get_new_row', options: ["expose"=>true])]
    public function app_hboy_get_new_row(EntityManagerInterface $entityManager,permission $permission): Response
    {
        if(! $permission->hasPermission('buyAdd',$this->bidObject,$this->getUser()))
            throw $this->createAccessDeniedException();
        $firstComod = $entityManager->getRepository('App:Commodity')->findOneBy(['bid'=>$this->bid]);
        $hbuyItem = new HbuyItem();
        $hbuyItem->setDes($firstComod->getDes());
        $form = $this->createForm(HbuyItemType::class,$hbuyItem,[
            'bid'=>$this->bid
        ]);
        $id = md5(random_bytes(10));
        return $this->json(
            [
                'view'=>$this->render('hbuy/hbuyNewRow.html.twig', [
                    'form'=>$form->createView(),
                    'commodity'=>$firstComod,
                    'id' => $id
                ]),
                'id'=>$id
            ]
        );
    }

    #[Route('/app/hbuy/item/new/pay', name: 'app_hboy_get_new_pay', options: ["expose"=>true])]
    public function app_hboy_get_new_pay(EntityManagerInterface $entityManager,permission $permission): Response
    {
        if(! $permission->hasPermission('buyAdd',$this->bidObject,$this->getUser()))
            throw $this->createAccessDeniedException();
        $hbuyPay = new HbuyPay();
        $hbuyPay->setAmount(1);
        $form = $this->createForm(HbuyPayType::class,$hbuyPay,[
            'bid'=>$this->bid
        ]);
        $id = md5(random_bytes(10));
        return $this->json(
            [
                'view'=>$this->render('hbuy/hbuyNewPay.html.twig', [
                    'form'=>$form->createView(),
                    'id' => $id
                ]),
                'id'=>$id
            ]
        );
    }
    #[Route('/app/hbuy/new', name: 'app_hbuy_new', options: ["expose"=>true])]
    public function app_hbuy_new(Log $log,permission $permission,Request $request,EntityManagerInterface $entityManager): Response
    {
        if(! $permission->hasPermission('buyAdd',$this->bidObject,$this->getUser()))
            throw $this->createAccessDeniedException();
        if(!$entityManager->getRepository('App:Commodity')->findOneBy(['bid'=>$this->bidObject])){
            $response['result'] = 0;
            $response['swal'] = [
                'text'=>'هنوز هیچ کالایی ثبت نشده است.لطفا ابتدا از بخش کالا و خدمات یک کالا را ثبت نمایید.',
                'confirmButtonText'=>'قبول',
                'icon'=>'error'
            ];
            $response['component'] = $this->generateUrl('app_bank_list');
            return $this->json($response);
        }
        $buy = new Hbuy();
        $form = $this->createForm(HbuyType::class,$buy,[
            'action'=>$this->generateUrl('app_hbuy_new'),
            'bid'=>$this->bidObject,
            'tax'=>$this->bidObject->getMaliyatafzode()
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $buy->setArzType($this->bidObject->getArzMain());
            $buy->setBid($this->bidObject);
            $buy->setDateSubmit(time());
            $buy->setSubmitter($this->getUser());
            $entityManager->persist($buy);
            $entityManager->flush();
            $items = json_decode($form->get('items')->getData(),true);
            foreach ($items as $item){
                //save items
                $tempItem = new HbuyItem();
                $tempItem->setHbuy($buy);
                $com = $entityManager->getRepository('App:Commodity')->find($item['item']);
                $tempItem->setCommodity($com);
                $tempItem->setPrice($item['price']);
                $tempItem->setNum($item['num']);
                $tempItem->setOff($item['off']);
                $entityManager->persist($tempItem);
                $entityManager->flush();
            }
            $log->add($this->bidObject,$this->getUser(),'web','خرید و هزینه','افزودن فاکتور خرید');
            
            $response['result'] = 1;
            $response['swal'] = [
                'text'=>'با موفقیت ثبت شد.',
                'confirmButtonText'=>'قبول',
                'icon'=>'success'
            ];
            $response['component'] = $this->generateUrl('app_hbuy_list');
            return $this->json($response);
        }

        return $this->json(
            [
                'view'=>$this->render('hbuy/hbuy.html.twig', [
                    'form' => $form->createView(),
                ]),
                'topView' => $this->render('hbuy/buttons.html.twig'),
                'title'=>'خرید جدید'

            ]
        );
    }
}
