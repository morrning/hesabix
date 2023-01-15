<?php

namespace App\Controller;

use App\Entity\HesabdariItem;
use App\Entity\Person;
use App\Entity\PersonRSFile;
use App\Entity\PersonRSOther;
use App\Entity\PersonRSPerson;
use App\Form\PersonRSCompactType;
use App\Form\PersonRSOtherType;
use App\Form\PersonRSPersonType;
use App\Form\PersonRSType;
use App\Form\PersonType;
use App\Service\hesabdari;
use App\Service\kernel;
use App\Service\pdfMGR;
use App\Service\permission;
use Doctrine\ORM\EntityManagerInterface;
use Proxies\__CG__\App\Entity\HesabdariFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Service\Log;

class ACPPersonsController extends AbstractController
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
        $this->activeYearObject = $entityManager->getRepository(\App\Entity\Year::class)->find($this->activeYear);
    }

    #[Route('/app/persons', name: 'acpPersons', options: ["expose"=>true])]
    public function acpPersons(permission $permission,Request $request, EntityManagerInterface $entityManager,kernel $kernel): Response
    {
        if( $permission->hasPermission('personEdit',$this->bidObject,$this->getUser()) ||  $permission->hasPermission('personDelete',$this->bidObject,$this->getUser()) )
        {
            $bid = $kernel->checkBID($request);
            if(!$bid){
                return $this->redirectToRoute('app_main');
            }
            return $this->json(
                [
                    'view'=>$this->render('app_main/acp_persons/list.html.twig', [
                        'persons' => $entityManager->getRepository(\App\Entity\Person::class)->getListAll($bid)
                    ]),
                    'topView' => $this->render('app_main/acp_persons/topButtons/buttons.html.twig'),
                    'title'=>'اشخاص'
                ]
            );
        }
        throw $this->createAccessDeniedException();
    }
    #[Route('/app/person/view/{id}', name: 'acpPersonView', options: ["expose"=>true])]
    public function acpPersonView($id,permission $permission,Request $request, EntityManagerInterface $entityManager,kernel $kernel): Response
    {
        if( $permission->hasPermission('personEdit',$this->bidObject,$this->getUser()) ||  $permission->hasPermission('personDelete',$this->bidObject,$this->getUser()) )
        {
            $bid = $kernel->checkBID($request);
            if(!$bid){
                return $this->redirectToRoute('app_main');
            }
            $person = $entityManager->getRepository(\App\Entity\Person::class)->find($id);
            if(!$person)
                throw $this->createNotFoundException();
            if($person->getBid()->getId() != $this->bid)
                throw $this->createAccessDeniedException();

            return $this->json(
                [
                    'view'=>$this->render('app_main/acp_persons/view.html.twig', [
                        'person' => $person
                    ]),
                    'topView' => $this->render('app_main/acp_persons/topButtons/buttons.html.twig'),
                    'title'=>'شخص: ' . $person->getNikename()
                ]
            );
        }
        throw $this->createAccessDeniedException();
    }
    #[Route('/app/persons/print', name: 'app_person_list_print', options: ["expose"=>true])]
    public function acpPersonsPrint(Log $log,permission $permission,Request $request,EntityManagerInterface $entityManager,pdfMGR $pdfMGR): Response
    {
        if (!$permission->hasPermission('personAdd', $this->bidObject, $this->getUser()))
            throw $this->createAccessDeniedException();
        $pdfMGR->streamTwig2PDF('app_main/acp_persons/list_pdf.html.twig',[
            'persons' => $entityManager->getRepository(\App\Entity\Person::class)->getListAll($this->bid),
            'bid'=>$this->bidObject,
            'page_title'=>'لیست اشخاص'
        ]);
    }

    #[Route('/app/person/new', name: 'acpPersonNew', options: ["expose"=>true])]
    public function acpPersonNew(Log $log,permission $permission,Request $request,EntityManagerInterface $entityManager,kernel $kernel): Response
    {
        if(! $permission->hasPermission('personAdd',$this->bidObject,$this->getUser()))
            throw $this->createAccessDeniedException();
        $person = new Person();
        $form = $this->createForm(PersonType::class,$person,[
            'action'=>$this->generateUrl('acpPersonNew')
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if($entityManager->getRepository(\App\Entity\Person::class)->personExist($this->bidObject->getId(), $form->get('nikeName')->getData())){
                $response['result'] = 0;
                $response['swal'] = [
                    'text'=>'فردی با اسم مستعار وارد شده قبلا ثبت شده است.',
                    'confirmButtonText'=>'قبول',
                    'icon'=>'info'
                ];
                return $this->json($response);
            }
            else{
                $person->setBid($this->bidObject);
                $person->setNum($entityManager->getRepository(\App\Entity\Business::class)->getNewNumberPerson($this->bid));
                $entityManager->persist($person);
                $entityManager->flush();
                $log->add($this->bidObject,$this->getUser(),'web','اشخاص','افزودن شخص ');
                $response['result'] = 1;
                $response['swal'] = [
                    'text'=>'با موفقیت افزوده شد.',
                    'confirmButtonText'=>'قبول',
                    'icon'=>'success'
                ];
                $response['component'] = $this->generateUrl('acpPersons',['bid'=>$this->bid]);
                return $this->json($response);
            }

        }
        return $this->json(
            [
                'view'=>$this->render('app_main/acp_persons/new.html.twig', [
                    'form' => $form->createView(),
                ]),
                'topView' => $this->render('app_main/acp_persons/topButtons/back.html.twig'),

                'title'=>'شخص جدید'

            ]
        );
    }

    #[Route('/app/person/edit/{id}', name: 'acpPersonEdit', options: ["expose"=>true])]
    public function acpPersonEdit($id,Log $log,permission $permission, Request $request, EntityManagerInterface $entityManager,kernel $kernel): Response
    {
        if(! $permission->hasPermission('personEdit',$this->bidObject,$this->getUser()))
            throw $this->createAccessDeniedException();
        $person = $entityManager->getRepository(\App\Entity\Person::class)->find($id);
        if(! $person)
            throw $this->createNotFoundException();

        $form = $this->createForm(PersonType::class,$person,[
            'action'=>$this->generateUrl('acpPersonEdit',['id'=>$id])
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $person->setBid($this->bidObject);
            $entityManager->persist($person);
            $entityManager->flush();
            $log->add($this->bidObject,$this->getUser(),'web','اشخاص','ویرایش شخص ');

            $response['result'] = 1;
            $response['swal'] = [
                'text'=>'با موفقیت ویرایش شد.',
                'confirmButtonText'=>'قبول',
                'icon'=>'success'
            ];
            $response['component'] = $this->generateUrl('acpPersons',['bid'=>$this->bid]);
            return $this->json($response);
        }
        return $this->json(
            [
                'view'=>$this->render('app_main/acp_persons/new.html.twig', [
                    'form' => $form->createView(),
                ]),
                'topView' => $this->render('app_main/acp_persons/topButtons/back.html.twig'),
                'title'=>'ویرایش شخص'

            ]
        );
    }

    #[Route('/app/person/delete/{id}', name: 'acpPersonDelete', options: ["expose"=>true])]
    public function acpPersonDelete($id,Log $log,permission $permission, Request $request, EntityManagerInterface $entityManager,kernel $kernel): Response
    {
        if(! $permission->hasPermission('personDelete',$this->bidObject,$this->getUser()))
            throw $this->createAccessDeniedException();
        $person = $entityManager->getRepository(\App\Entity\Person::class)->find($id);
        if($person  ){
            $canDelete = true;
            //check rs person
            $files = $entityManager->getRepository(HesabdariItem::class)->findOneBy(['person'=>$person]);
            if($files){
                $canDelete = false;
            }

            if($canDelete){
                $entityManager->remove($person);
                $entityManager->flush();
                $log->add($this->bidObject,$this->getUser(),'web','اشخاص','حذف شخص ');
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

    #[Route('/app/person/receive/new', name: 'acpPersonreceiveNew', options: ["expose"=>true])]
    public function acpPersonreceiveNew(Log $log,permission $permission,Request $request,hesabdari $hesabdari,EntityManagerInterface $entityManager,kernel $kernel): Response
    {
        if(! $permission->hasPermission('personRSAdd',$this->bidObject,$this->getUser()))
            throw $this->createAccessDeniedException();

        $year = $kernel->checkActiveYear($request);
        if($year){
            if(!$year->getActive()){
                return $kernel->msgNotActiveYear();
            }
        }
        $anyBank = $entityManager->getRepository(\App\Entity\BanksAccount::class)->findOneBy(['bussiness'=>$this->bid]);
        if(!$anyBank){
            $response = [];
            $response['result'] = 1;
            $response['swal'] = [
                'text'=>'هنوز هیچ حساب بانکی اضافه نشده است. لطفا ابتدا یک مورد را ایجاد کنید.',
                'confirmButtonText'=>'قبول',
                'icon'=>'warning'
            ];
            $response['component'] = $this->generateUrl('app_bank_new');
            return $this->json($response);
        }
        $persons = $entityManager->getRepository(\App\Entity\Person::class)->getListAll($this->bidObject);
        if(count($persons) == 0){
            $response = [];
            $response['result'] = 1;
            $response['swal'] = [
                'text'=>'هنوز هیچ شخصی اضافه نشده است. لطفا ابتدا شخص جدیدی را ایجاد کنید.',
                'confirmButtonText'=>'قبول',
                'icon'=>'warning'
            ];
            $response['component'] = $this->generateUrl('acpPersons',['bid'=>$this->bid]);
            return $this->json($response);
        }
        $rs = ['test'];
        $form = $this->createForm(PersonRSCompactType::class,$rs,[
            'action'=>$this->generateUrl('acpPersonreceiveNew'),
            'bid'=>$this->bid
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if($form->get('amount')->getData()<=0){
                $response['result'] = 0;
                $response['modal-stay'] = 1;
                $response['swal'] = [
                    'text'=>'مبلغ وارد شده نا معتبر است',
                    'confirmButtonText'=>'قبول',
                    'icon'=>'error'
                ];
                return $this->json($response);
            }

            $file = new HesabdariFile;
            $file->setType('person_receive');
            $file->setArzType($this->bidObject->getArzMain());
            $file->setBid($this->bidObject);
            $file->setCanEdit(false);
            $file->setDate(time());
            $file->setSubmitter($this->getUser());
            $file->setYear($this->activeYearObject);
            $file->setDes($form->get('des')->getData());
            $entityManager->persist($file);
            $entityManager->flush();

            //set person part with code 14001
            $table = $entityManager->getRepository(\App\Entity\HesabdariTable::class)->findOneBy(['code' => 14001]);
            $h1 = new HesabdariItem();
            $h1->setBs($form->get('amount')->getData());
            $h1->setFile($file);
            $h1->setPerson($form->get('person')->getData());
            $h1->setCode($table);
            $h1->setDes($table->getName() . ' : ' . $form->get('person')->getData()->getNikeName());
            $entityManager->persist($h1);
            $entityManager->flush();

            //set bank part with code 14002
            $table = $entityManager->getRepository(\App\Entity\HesabdariTable::class)->findOneBy(['code' => 14002]);
            $h2 = new HesabdariItem();
            $h2->setBd($form->get('amount')->getData());
            $h2->setFile($file);
            $h2->setBank($form->get('data')->getData());
            $h2->setCode($table);
            $h2->setDes($table->getName() . ' : ' . $form->get('data')->getData()->getName());
            $entityManager->persist($h2);
            $entityManager->flush();
            
            //add log
            $log->add($this->bidObject,$this->getUser(),'web','اشخاص','افزودن دریافت از اشخاص');

            $response = [];
            $response['result'] = 1;
            $response['swal'] = [
                'text'=>'با موفقیت ثبت شد.',
                'confirmButtonText'=>'قبول',
                'icon'=>'success'
            ];
            $response['component'] = $this->generateUrl('acp_receive_list');
            return $this->json($response);
        }
        return $this->json(
            [
                'view'=>$this->render('app_main/acp_persons/rs/new.html.twig', [
                    'form' => $form->createView(),
                ]),
                'topView' => $this->render('app_main/acp_persons/rs/buttons.html.twig'),

                'title'=>'دریافت از اشخاص'

            ]
        );
    }

    #[Route('/app/person/receive/list', name: 'acp_receive_list', options: ["expose"=>true])]
    public function acp_receive_list(permission $permission,Request $request, EntityManagerInterface $entityManager,kernel $kernel): Response
    {
        if(! $permission->hasPermission('personRSDelete',$this->bidObject,$this->getUser()))
            throw $this->createAccessDeniedException();
        //echo $this->activeYear->getId();
        return $this->json(
            [
                'view'=>$this->render('app_main/acp_persons/rs/receive_list.html.twig', [
                    'files' => $entityManager->getRepository(\App\Entity\HesabdariFile::class)->getListAll($this->bid,$this->activeYear,'person_receive')
                ]),
                'topView' => $this->render('app_main/acp_persons/rs/buttons.html.twig'),
                'title'=>'لیست دریافت از اشخاص'
            ]
        );
    }

    #[Route('/app/person/resend/list/print', name: 'app_person_rs_list_print', options: ["expose"=>true])]
    public function app_person_rs_list_print(pdfMGR $pdfMGR, permission $permission,Request $request, EntityManagerInterface $entityManager,kernel $kernel): Response
    {
        if(! $permission->hasPermission('personRSPrint',$this->bidObject,$this->getUser()))
            throw $this->createAccessDeniedException();
        $pdfMGR->streamTwig2PDF('app_main/acp_persons/rs/pdf/list.html.twig',[
            'datas' => $entityManager->getRepository(\App\Entity\HesabdariFile::class)->getListAll($this->bid,$this->activeYear,'person_receive'),
            'bid'=>$this->bidObject,
            'page_title'=>'لیست دریافت و پرداخت‌ها'
        ]);

        return $this->json(
            [
                'view'=>$this->render('app_main/acp_persons/rs/list.html.twig', [
                    'files' => $entityManager->getRepository('App:PersonRSFile')->getListAll($this->bid)
                ]),
                'topView' => $this->render('app_main/acp_persons/rs/buttons.html.twig'),
                'title'=>'لیست دریافت و پرداخت‌ها'
            ]
        );
    }

    #[Route('/app/person/receive/delete/{id}', name: 'acpPersonReceiveDelete', options: ["expose"=>true])]
    public function acpPersonReceiveDelete($id,Log $log, permission $permission, Request $request, EntityManagerInterface $entityManager,kernel $kernel): Response
    {
        if(! $permission->hasPermission('personRSDelete',$this->bidObject,$this->getUser()))
            throw $this->createAccessDeniedException();
        $file = $entityManager->getRepository(\App\Entity\HesabdariFile::class)->findOneBy(['id'=>$id,'type'=>'person_receive']);
        if($file){
            $entityManager->remove($file);
            $entityManager->flush();
            $log->add($this->bidObject,$this->getUser(),'web','اشخاص','حذف دریافت از شخص');

        }
        return $this->json(
            [
                'result'=>1
            ]
        );
    }

    #[Route('/app/persons/transactions/{type}/{id}', name: 'app_person_transactions', options: ["expose"=>true])]
    public function app_person_transactions(permission $permission,Request $request, hesabdari $hesabdari,EntityManagerInterface $entityManager,kernel $kernel,$type, $id = 0): Response
    {
        if( $permission->hasPermission('personPrint',$this->bidObject,$this->getUser()) ||  $permission->hasPermission('personDelete',$this->bidObject,$this->getUser()) )
        {
            if($id){

            }
            $persons = $entityManager->getRepository(\App\Entity\Person::class)->getListAll($this->bidObject);
            if(count($persons) == 0){
                $response = [];
                $response['result'] = 1;
                $response['swal'] = [
                    'text'=>'هنوز هیچ شخصی اضافه نشده است. لطفا ابتدا شخص جدیدی را ایجاد کنید.',
                    'confirmButtonText'=>'قبول',
                    'icon'=>'warning'
                ];
                $response['component'] = $this->generateUrl('acpPersons',['bid'=>$this->bid]);
                return $this->json($response);
            }
            if($type == 'all'){
                return $this->json(
                    [
                        'view'=>$this->render('app_main/acp_persons/card.html.twig', [
                            'persons' => $persons,
                            'datas' => $hesabdari->getTransactionsByRef('person:' . $this->bid . ':' . $persons[0]->getId()),
                            'active' => $persons[0]->getId()
                        ]),
                        'topView' => $this->render('app_main/acp_persons/topButtons/buttons.html.twig'),
                        'title'=>'کارت حساب اشخاص'
                    ]
                );
            }
            elseif ($type == 'load'){
                return $this->json(
                    [
                        'view'=>$this->render('app_main/acp_persons/card-transactions.html.twig', [
                            'datas' => $hesabdari->getTransactionsByRef('person:' . $this->bid . ':' . $id),
                        ]),
                        'topView' => $this->render('app_main/acp_persons/topButtons/buttons.html.twig'),
                        'title'=>'کارت حساب اشخاص'
                    ]
                );
            }

        }
        throw $this->createAccessDeniedException();
    }

    #[Route('/app/persons/transactions_print/{id}', name: 'app_person_transactions_print', options: ["expose"=>true])]
    public function app_person_transactions_print(permission $permission,pdfMGR $pdfMGR, hesabdari $hesabdari,EntityManagerInterface $entityManager,kernel $kernel,$id): Response
    {
        if( $permission->hasPermission('personPrint',$this->bidObject,$this->getUser()) ||  $permission->hasPermission('personDelete',$this->bidObject,$this->getUser()) )
        {
            $pdfMGR->streamTwig2PDF('app_main/acp_persons/card_print.html.twig',[
                'datas' => $hesabdari->getTransactionsByRef('person:' . $this->bid . ':' . $id),
                'bid'=>$this->bidObject,
                'page_title'=>'کارت حساب ' . $entityManager->getRepository(\App\Entity\Person::class)->find($id)->getNikeName()
            ]);
        }
        throw $this->createAccessDeniedException();
    }

}
