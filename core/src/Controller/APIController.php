<?php

namespace App\Controller;

use App\Entity\API;
use App\Service\kernel;
use App\Service\permission;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class APIController extends AbstractController
{
    private $bid = null;
    private $request = null;
    private $bidObject = null;

    /**
     * function to generate random strings
     * @param 		int 	$length 	number of characters in the generated string
     * @return 		string	a new string is created with random characters of the desired length
     */
    private function RandomString($length = 32) {
        return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
    }

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
    #[Route('/app/api/list', name: 'app_api_home', options: ["expose"=>true])]
    public function app_api_home(permission $permission,EntityManagerInterface $entityManager): Response
    {
        if(! $permission->hasPermission('admin',$this->bidObject,$this->getUser()))
            throw $this->createAccessDeniedException();
        return $this->json(
            [
                'view'=>$this->render('api/list.html.twig', [
                    'datas' => $entityManager->getRepository('App:API')->findBy(['bid'=>$this->bid])
                ]),
                'title'=>'رابط برنامه نویسی API'
            ]
        );
    }

    #[Route('/app/api/new', name: 'app_api_new', options: ["expose"=>true])]
    public function app_api_new(permission $permission,EntityManagerInterface $entityManager): Response
    {
        if(! $permission->hasPermission('admin',$this->bidObject,$this->getUser()))
            throw $this->createAccessDeniedException();
        $api = new API();
        $api->setBid($this->bidObject);
        $api->setUser($this->getUser());
        $api->setDateSubmit(time());
        $api->getDes('رابط برنامه');
        $api->setCode($this->RandomString('24'));
        $entityManager->persist($api);
        $entityManager->flush();

        return $this->json(
            [
                'result'=>1,

            ]
        );
    }

    #[Route('/app/api/delete/{id}', name: 'app_api_remove', options: ["expose"=>true])]
    public function app_api_remove(permission $permission,EntityManagerInterface $entityManager, $id): Response
    {
        if(! $permission->hasPermission('admin',$this->bidObject,$this->getUser()))
            throw $this->createAccessDeniedException();
        $api = $entityManager->getRepository('App:API')->find($id);
        if(! $api)
            throw $this->createNotFoundException();
        if($api->getBid() != $this->bidObject)
            throw $this->createAccessDeniedException();
        $entityManager->getRepository('App:API')->remove($api);

        return $this->json(
            [
                'result'=>1,

            ]
        );
    }

    #[Route('/core/user/disable-guide', name: 'app_api_disable_guide', options: ["expose"=>true])]
    public function app_api_disable_guide(permission $permission,EntityManagerInterface $entityManager): Response
    {
        if($this->getUser()){
            $user = $this->getUser();
            $user->setGuide(true);
            $entityManager->persist($user);
            $entityManager->flush();
        }
        return $this->json(
            [
                'data'=>true
            ]
        );
    }
}
