<?php

namespace App\Controller;

use App\Entity\HesabdariItem;
use App\Form\HesabdariItemType;
use App\Repository\BanksAccountRepository;
use App\Repository\TankhahAccountRepository;
use App\Service\kernel;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HesabdariController extends AbstractController
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

    #[Route('/hesabdari/new', name: 'app_hesabdari_new', options: ["expose"=>true])]
    public function app_hesabdari_new(): Response
    {
        return $this->json(
            [
                'view'=>$this->render('hesabdari/new.html.twig', [
                    'bussiness' => $this->bidObject
                ]),
                'title'=>'سند حسابداری جدید'
            ]
        );
    }
    #[Route('/hesabdari/get/hesabdari/item', name: 'app_hesabdari_get_item_template', options: ["expose"=>true])]
    public function app_hesabdari_get_item_template(): Response
    {
        $formItem = $this->createForm(HesabdariItemType::class, new HesabdariItem());
        $id = md5(random_bytes(10));
        return $this->json(
            [
                'view'=>$this->render('hesabdari/newFormItem.html.twig', [
                    'formItem' => $formItem->createView(),
                    'id' => $id
                ]),
                'id'=>$id
            ]
        );
    }

    #[Route('/hesabdari/get/hesabdari/type/{ref}', name: 'app_hesabdari_get_type', options: ["expose"=>true])]
    public function app_hesabdari_get_type($ref,Request $request,kernel $kernel): Response
    {
        $bid = $kernel->checkBID($request);
        if(!$bid){
            return $this->redirectToRoute('app_main');
        }
        //check if bank selected
        if($ref == 10001){
            //bank reference
            $defaultData = ['message' => $bid];
            $form = $this->createFormBuilder($defaultData)
                ->add('name', EntityType::class,[
                    'class'=>\App\Entity\BanksAccount::class,
                    'choice_label' => 'name',
                    'choice_value' => 'id',
                    'query_builder' => function (BanksAccountRepository $er) use ($bid) {
                        return $er->createQueryBuilder('u')
                            ->where('u.bussiness=:bid')
                            ->setParameter('bid',$bid);
                    },
                ])
                ->getForm();
        }
        elseif($ref == 10002){
            //tankhah reference
            $defaultData = ['message' => $bid];
            $form = $this->createFormBuilder($defaultData)
                ->add('name', EntityType::class,[
                    'class'=>\App\Entity\TankhahAccount::class,
                    'choice_label' => 'name',
                    'choice_value' => 'id',
                    'query_builder' => function (TankhahAccountRepository $er) use ($bid) {
                        return $er->createQueryBuilder('u')
                            ->where('u.bussiness=:bid')
                            ->setParameter('bid',$bid);
                    },
                ])
                ->getForm();
        }
        else{
            $defaultData = ['message' => 'Type your message here'];
            $form = $this->createFormBuilder($defaultData)
                ->add('name', TextType::class)
                ->getForm();
        }
        return $this->json(
            [
                'content'=>$this->renderForm('form-single.html.twig',['form' => $form]),
                'id'=>$ref
                ]
        );
    }

    #[Route('/hesabdari/document/insert', name: 'app_hesabdari_document_insert', options: ["expose"=>true])]
    public function app_hesabdari_document_insert(Request $request): Response
    {
        $data = $request->get('data');

        return $this->json(
            [
                'view'=>$data,
            ]
        );
    }
}
