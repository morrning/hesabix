<?php

namespace App\Controller;

use App\Entity\BanksAccount;
use App\Entity\Hbuy;
use App\Entity\HbuyItem;
use App\Form\BankType;
use App\Form\HbuyItemType;
use App\Form\HbuyType;
use App\Service\kernel;
use App\Service\Log;
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

    #[Route('/app/hbuy/item/new/row', name: 'app_hboy_get_new_row', options: ["expose"=>true])]
    public function app_hboy_get_new_row(): Response
    {
        $hbuyItem = new HbuyItem();
        $form = $this->createForm(HbuyItemType::class,$hbuyItem);
        $id = md5(random_bytes(10));
        return $this->json(
            [
                'view'=>$this->render('hbuy/hbuyNewRow.html.twig', [
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
        if(! $permission->hasPermission('bankAdd',$this->bidObject,$this->getUser()))
            throw $this->createAccessDeniedException();
        $buy = new Hbuy();
        $form = $this->createForm(HbuyType::class,$buy,[
            'action'=>$this->generateUrl('app_hbuy_new'),
            'bid'=>$this->bid
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $buy->setArzType($this->bidObject->getArzMain());
            $buy->setBid($this->bidObject);
            $entityManager->persist($buy);
            $entityManager->flush();
            $log->add($this->bidObject,$this->getUser(),'web','خرید و هزینه','افزودن فاکتور خرید');

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
                'view'=>$this->render('hbuy/hbuy.html.twig', [
                    'form' => $form->createView(),
                ]),
                'topView' => $this->render('app_main/bank/topButtons/buttons.html.twig'),

                'title'=>'خرید جدید'

            ]
        );
    }
}
