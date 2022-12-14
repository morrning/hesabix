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
        $this->bidObject = $entityManager->getRepository('App:Business')->find($this->bid);
        if (! $this->bidObject)
            throw $this->createNotFoundException();
        $this->activeYear = $kernel->checkActiveYear($this->request);
        if(!$this->activeYear){
            throw $this->createNotFoundException();
        }
        $this->activeYearObject = $entityManager->getRepository('App:Year')->find($this->activeYear);
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
                        'persons' => $entityManager->getRepository('App:Person')->getListAll($bid)
                    ]),
                    'topView' => $this->render('app_main/acp_persons/topButtons/buttons.html.twig'),
                    'title'=>'??????????'
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
            $person = $entityManager->getRepository('App:Person')->find($id);
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
                    'title'=>'??????: ' . $person->getNikename()
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
            'persons' => $entityManager->getRepository('App:Person')->getListAll($this->bid),
            'bid'=>$this->bidObject,
            'page_title'=>'???????? ??????????'
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
            if($entityManager->getRepository('App:Person')->personExist($this->bidObject->getId(), $form->get('nikeName')->getData())){
                $response['result'] = 0;
                $response['swal'] = [
                    'text'=>'???????? ???? ?????? ???????????? ???????? ?????? ???????? ?????? ?????? ??????.',
                    'confirmButtonText'=>'????????',
                    'icon'=>'info'
                ];
                return $this->json($response);
            }
            else{
                $person->setBid($this->bidObject);
                $person->setNum($entityManager->getRepository('App:Business')->getNewNumberPerson($this->bid));
                $entityManager->persist($person);
                $entityManager->flush();
                $log->add($this->bidObject,$this->getUser(),'web','??????????','???????????? ?????? ');
                $response['result'] = 1;
                $response['swal'] = [
                    'text'=>'???? ???????????? ???????????? ????.',
                    'confirmButtonText'=>'????????',
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

                'title'=>'?????? ????????'

            ]
        );
    }

    #[Route('/app/person/edit/{id}', name: 'acpPersonEdit', options: ["expose"=>true])]
    public function acpPersonEdit($id,Log $log,permission $permission, Request $request, EntityManagerInterface $entityManager,kernel $kernel): Response
    {
        if(! $permission->hasPermission('personEdit',$this->bidObject,$this->getUser()))
            throw $this->createAccessDeniedException();
        $person = $entityManager->getRepository('App:Person')->find($id);
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
            $log->add($this->bidObject,$this->getUser(),'web','??????????','???????????? ?????? ');

            $response['result'] = 1;
            $response['swal'] = [
                'text'=>'???? ???????????? ???????????? ????.',
                'confirmButtonText'=>'????????',
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
                'title'=>'???????????? ??????'

            ]
        );
    }

    #[Route('/app/person/delete/{id}', name: 'acpPersonDelete', options: ["expose"=>true])]
    public function acpPersonDelete($id,Log $log,permission $permission, Request $request, EntityManagerInterface $entityManager,kernel $kernel): Response
    {
        if(! $permission->hasPermission('personDelete',$this->bidObject,$this->getUser()))
            throw $this->createAccessDeniedException();
        $person = $entityManager->getRepository('App:Person')->find($id);
        if($person  ){
            $canDelete = true;
            //check rs person
            $files = $entityManager->getRepository('App:PersonRSPerson')->findOneBy(['person'=>$person]);
            if($files){
                $canDelete = false;
            }

            if($canDelete){
                $entityManager->remove($person);
                $entityManager->flush();
                $log->add($this->bidObject,$this->getUser(),'web','??????????','?????? ?????? ');
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

    #[Route('/app/person/ressend/new', name: 'acpPersonReciveSendNew', options: ["expose"=>true])]
    public function acpPersonReciveSendNew(Log $log,permission $permission,Request $request,hesabdari $hesabdari,EntityManagerInterface $entityManager,kernel $kernel): Response
    {
        if(! $permission->hasPermission('personRSAdd',$this->bidObject,$this->getUser()))
            throw $this->createAccessDeniedException();

        $year = $kernel->checkActiveYear($request);
        if($year){
            if(!$year->getActive()){
                return $kernel->msgNotActiveYear();
            }
        }
        $anyBank = $entityManager->getRepository('App:BanksAccount')->findOneBy(['bussiness'=>$this->bid]);
        if(!$anyBank){
            $response = [];
            $response['result'] = 1;
            $response['swal'] = [
                'text'=>'???????? ?????? ???????? ?????????? ?????????? ???????? ??????. ???????? ?????????? ???? ???????? ???? ?????????? ????????.',
                'confirmButtonText'=>'????????',
                'icon'=>'warning'
            ];
            $response['component'] = $this->generateUrl('app_bank_new');
            return $this->json($response);
        }
        $persons = $entityManager->getRepository('App:Person')->getListAll($this->bidObject);
        if(count($persons) == 0){
            $response = [];
            $response['result'] = 1;
            $response['swal'] = [
                'text'=>'???????? ?????? ???????? ?????????? ???????? ??????. ???????? ?????????? ?????? ?????????? ???? ?????????? ????????.',
                'confirmButtonText'=>'????????',
                'icon'=>'warning'
            ];
            $response['component'] = $this->generateUrl('acpPersons',['bid'=>$this->bid]);
            return $this->json($response);
        }
        $rs = ['test'];
        $form = $this->createForm(PersonRSCompactType::class,$rs,[
            'action'=>$this->generateUrl('acpPersonReciveSendNew'),
            'bid'=>$this->bid
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if($form->get('amount')->getData()<=0){
                $response['result'] = 0;
                $response['modal-stay'] = 1;
                $response['swal'] = [
                    'text'=>'???????? ???????? ?????? ???? ?????????? ??????',
                    'confirmButtonText'=>'????????',
                    'icon'=>'error'
                ];
                return $this->json($response);
            }

            //first create person;
            $personFile = new PersonRSFile();
            $personFile->setSubmitter($this->getUser());
            $personFile->setDateSubmit(time());
            $personFile->setBid($this->bidObject);
            $personFile->setDes($form->get('des')->getData());
            $personFile->setDateSave($form->get('dateSave')->getData());
            $personFile->setRS($form->get('RS')->getData());
            $personFile->setYear($year);
            $entityManager->persist($personFile);
            $entityManager->flush();
            $personPerson = new PersonRSPerson();
            $personPerson->setAmount($form->get('amount')->getData());
            $personPerson->setPerson($form->get('person')->getData());
            $personPerson->setFile($personFile);
            $entityManager->persist($personPerson);
            $entityManager->flush();
            $personOther = new PersonRSOther();
            $personOther->setFile($personFile);
            $personOther->setAmount($form->get('amount')->getData());
            $personOther->setData($form->get('data')->getData()->getId());
            $personOther->setType($form->get('type')->getData());
            $personOther->setBank($form->get('data')->getData());
            $entityManager->persist($personOther);
            $entityManager->flush();
            //add hesabdari file
            $ref = 'rs:' . $this->bid . ':' . $personFile->getId();
            $pitem = new HesabdariItem();
            $oitem = new HesabdariItem();
            $pitem->setType('person');
            $pitem->setTypeData($personPerson->getPerson()->getId());
            $pitem->setDes($personPerson->getPerson()->getNikeName());
            $oitem->setType('bank');
            $oitem->setTypeData($personOther->getBank()->getId());
            if($personFile->getRS()){
                $pitem->setDes('???????????? ?????? ???? ' . $personPerson->getPerson()->getNikeName() . ' : ' . $personFile->getDes());
                $pitem->setBs($form->get('amount')->getData());
                $pitem->setBd(0);
                $oitem->setBs(0);
                $oitem->setBd($form->get('amount')->getData());
                $pitem->setCode($entityManager->getRepository('App:HesabdariTable')->findOneBy(['code'=>15001]));
            }
            else{
                $pitem->setBs(0);
                $pitem->setBd($form->get('amount')->getData());
                $oitem->setBs($form->get('amount')->getData());
                $oitem->setBd(0);
                $pitem->setDes('???????????? ?????? ???? ' . $personPerson->getPerson()->getNikeName() . ' : ' . $personFile->getDes());
                $pitem->setCode($entityManager->getRepository('App:HesabdariTable')->findOneBy(['code'=>18002]));

            }


            $oitem->setCode($entityManager->getRepository('App:HesabdariTable')->findOneBy(['code'=>10001]));
            $oitem->setDes($pitem->getDes());

            $des = '???????????? ?? ???????????? ???? ?????????? : ' . $personPerson->getPerson()->getNikeName() . ' ??????: ' . $personFile->getDes();
            $personFile->setHesab(
                $hesabdari->insertNewDoc($this->getUser(),$personFile->getDateSave(),$des,$this->bidObject,$ref,[$pitem,$oitem])
            );
            $entityManager->persist($personFile);
            $entityManager->flush();
            $log->add($this->bidObject,$this->getUser(),'web','??????????','???????????? ???????????? ?? ???????????? ');

            $response = [];
            $response['result'] = 1;
            $response['swal'] = [
                'text'=>'???? ???????????? ?????? ????.',
                'confirmButtonText'=>'????????',
                'icon'=>'success'
            ];
            $response['component'] = $this->generateUrl('acp_rs_list');
            return $this->json($response);
        }
        return $this->json(
            [
                'view'=>$this->render('app_main/acp_persons/rs/new.html.twig', [
                    'form' => $form->createView(),
                ]),
                'topView' => $this->render('app_main/acp_persons/rs/buttons.html.twig'),

                'title'=>'???????????? ?? ???????????? ????????'

            ]
        );
    }

    #[Route('/app/person/resend/list', name: 'acp_rs_list', options: ["expose"=>true])]
    public function acp_rs_list(permission $permission,Request $request, EntityManagerInterface $entityManager,kernel $kernel): Response
    {
        if(! $permission->hasPermission('personRSDelete',$this->bidObject,$this->getUser()))
            throw $this->createAccessDeniedException();
        //echo $this->activeYear->getId();
        return $this->json(
            [
                'view'=>$this->render('app_main/acp_persons/rs/list.html.twig', [
                    'files' => $entityManager->getRepository('App:PersonRSFile')->getListAll($this->bid,$this->activeYear)
                ]),
                'topView' => $this->render('app_main/acp_persons/rs/buttons.html.twig'),
                'title'=>'???????????? ?? ???????????????????'
            ]
        );
    }

    #[Route('/app/person/resend/list/print', name: 'app_person_rs_list_print', options: ["expose"=>true])]
    public function app_person_rs_list_print(pdfMGR $pdfMGR, permission $permission,Request $request, EntityManagerInterface $entityManager,kernel $kernel): Response
    {
        if(! $permission->hasPermission('personRSPrint',$this->bidObject,$this->getUser()))
            throw $this->createAccessDeniedException();
        $pdfMGR->streamTwig2PDF('app_main/acp_persons/rs/pdf/list.html.twig',[
            'datas' => $entityManager->getRepository('App:PersonRSFile')->getListAll($this->bid,$this->activeYear),
            'bid'=>$this->bidObject,
            'page_title'=>'???????? ???????????? ?? ???????????????????'
        ]);

        return $this->json(
            [
                'view'=>$this->render('app_main/acp_persons/rs/list.html.twig', [
                    'files' => $entityManager->getRepository('App:PersonRSFile')->getListAll($this->bid)
                ]),
                'topView' => $this->render('app_main/acp_persons/rs/buttons.html.twig'),
                'title'=>'???????? ???????????? ?? ???????????????????'
            ]
        );
    }

    #[Route('/app/person/ressend/delete/{id}', name: 'acpPersonRessendDelete', options: ["expose"=>true])]
    public function acpPersonRessendDelete($id,Log $log, permission $permission, Request $request, EntityManagerInterface $entityManager,kernel $kernel): Response
    {
        if(! $permission->hasPermission('personRSDelete',$this->bidObject,$this->getUser()))
            throw $this->createAccessDeniedException();
        $file = $entityManager->getRepository('App:PersonRSFile')->find($id);
        if($file){
            $entityManager->remove($file);
            $entityManager->flush();
            $log->add($this->bidObject,$this->getUser(),'web','??????????','?????? ???????????? ?? ???????????? ');

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
            $persons = $entityManager->getRepository('App:Person')->getListAll($this->bidObject);
            if(count($persons) == 0){
                $response = [];
                $response['result'] = 1;
                $response['swal'] = [
                    'text'=>'???????? ?????? ???????? ?????????? ???????? ??????. ???????? ?????????? ?????? ?????????? ???? ?????????? ????????.',
                    'confirmButtonText'=>'????????',
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
                        'title'=>'???????? ???????? ??????????'
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
                        'title'=>'???????? ???????? ??????????'
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
                'page_title'=>'???????? ???????? ' . $entityManager->getRepository('App:Person')->find($id)->getNikeName()
            ]);
        }
        throw $this->createAccessDeniedException();
    }

}
