<?php

namespace App\Controller;

use App\Entity\Commodity;
use App\Form\CommodityType;
use App\Service\kernel;
use App\Service\Log;
use App\Service\permission;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommodityController extends AbstractController
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

    #[Route('/app/commodity/new', name: 'app_commodity_new', options: ["expose"=>true])]
    public function app_commodity_new(Log $log,permission $permission,Request $request,EntityManagerInterface $entityManager): Response
    {
        if(! $permission->hasPermission('commodityAdd',$this->bidObject,$this->getUser()))
            throw $this->createAccessDeniedException();
        $commodity = new Commodity();
        $form = $this->createForm(CommodityType::class,$commodity,[
            'action'=>$this->generateUrl('app_commodity_new')
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if($form->get('priceBuy')->getData()<0 or $form->get('priceSell')->getData()<0){
                $response['result'] = 0;
                $response['modal-stay'] = 1;
                $response['swal'] = [
                    'text'=>'مبلغ وارد شده نا معتبر است',
                    'confirmButtonText'=>'قبول',
                    'icon'=>'error'
                ];
                return $this->json($response);
            }
            $commodity->setCode($entityManager->getRepository('App:Business')->getNewNumberCommodity($this->bid));
            $commodity->setBid($this->bidObject);
            $entityManager->persist($commodity);
            $entityManager->flush();
            $log->add($this->bidObject,$this->getUser(),'web','کالا و خدمات','افزودن کالا و خدمات');

            $response['result'] = 1;
            $response['swal'] = [
                'text'=>'با موفقیت ثبت شد.',
                'confirmButtonText'=>'قبول',
                'icon'=>'success'
            ];
            $response['component'] = $this->generateUrl('app_commodity_list');
            return $this->json($response);
        }
        return $this->json(
            [
                'view'=>$this->render('app_main/commodity/new.html.twig', [
                    'form' => $form->createView(),
                ]),
                'topView' => $this->render('app_main/commodity/topButtons/buttons.html.twig'),

                'title'=>'کالا خدمات جدید'

            ]
        );
    }

    #[Route('/api/commodity/get/{id}', name: 'api_commodity_get', options: ["expose"=>true])]
    public function api_commodity_get($id,Log $log,permission $permission,Request $request,EntityManagerInterface $entityManager): Response
    {
        if(! $permission->hasPermission('commodityEdit',$this->bidObject,$this->getUser()))
            throw $this->createAccessDeniedException();
        $commodity = $entityManager->getRepository('App:Commodity')->find($id);
        if(! $commodity)
            return $this->json(['result'=>0]);

        return $this->json(
            [
                'name'=>$commodity->getName(),
                'price_sell'=>$commodity->getPriceSell(),
                'price_buy'=>$commodity->getPriceBuy(),
                'des'=>$commodity->getDes(),
                'unit'=>$commodity->getUnit()->getName()
            ]
        );
    }
    #[Route('/app/commodity/edit/{id}', name: 'app_commodity_edit', options: ["expose"=>true])]
    public function app_commodity_edit($id,Log $log,permission $permission,Request $request,EntityManagerInterface $entityManager): Response
    {
        if(! $permission->hasPermission('commodityEdit',$this->bidObject,$this->getUser()))
            throw $this->createAccessDeniedException();
        $commodity = $entityManager->getRepository('App:Commodity')->find($id);
        if(! $commodity)
            throw $this->createNotFoundException();

        $form = $this->createForm(CommodityType::class,$commodity,[
            'action'=>$this->generateUrl('app_commodity_edit',['id'=>$id])
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($commodity);
            $entityManager->flush();
            $log->add($this->bidObject,$this->getUser(),'web','کالا و خدمات','ویرایش کالا و خدمات');

            $response['result'] = 1;
            $response['swal'] = [
                'text'=>'با موفقیت ویرایش شد.',
                'confirmButtonText'=>'قبول',
                'icon'=>'success'
            ];
            $response['component'] = $this->generateUrl('app_commodity_list');
            return $this->json($response);
        }
        return $this->json(
            [
                'view'=>$this->render('app_main/commodity/new.html.twig', [
                    'form' => $form->createView(),
                ]),
                'topView' => $this->render('app_main/commodity/topButtons/buttons.html.twig'),

                'title'=>'ویرایش :' . $commodity->getName()

            ]
        );
    }

    #[Route('/app/commodity/list', name: 'app_commodity_list', options: ["expose"=>true])]
    public function app_commodity_list(permission $permission, Request $request,EntityManagerInterface $entityManager): Response
    {
        if( $permission->hasPermission('commodityEdit',$this->bidObject,$this->getUser()) ||  $permission->hasPermission('commodityDelete',$this->bidObject,$this->getUser()) )
        {
            return $this->json(
                [
                    'view'=>$this->render('app_main/commodity/list.html.twig', [
                        'comms' => $entityManager->getRepository('App:Commodity')->getListAll($this->bid)
                    ]),
                    'topView' => $this->render('app_main/commodity/topButtons/buttons.html.twig'),
                    'title'=>'کالاها و خدمات'
                ]
            );
        }
        throw $this->createAccessDeniedException();

    }

    #[Route('/app/commodity/delete/{id}', name: 'app_commodity_delete', options: ["expose"=>true])]
    public function app_commodity_delete($id,Log $log,permission $permission, Request $request, EntityManagerInterface $entityManager,kernel $kernel): Response
    {
        if(! $permission->hasPermission('commodityDelete',$this->bidObject,$this->getUser()))
            throw $this->createAccessDeniedException();
        $person = $entityManager->getRepository('App:Commodity')->find($id);
        if($person){
            if($this->bidObject->getOwner() == $this->getUser()){
                $entityManager->remove($person);
                $entityManager->flush();
                $log->add($this->bidObject,$this->getUser(),'web','کالا و خدمات','حذف کالا و خدمات');

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
}
