<?php

namespace App\Controller;


use App\Form\StackNewContentType;
use App\Form\StackNewReplayType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service;

class StackController extends AbstractController
{
    /**
     * function to generate random strings
     * @param 		int 	$length 	number of characters in the generated string
     * @return 		string	a new string is created with random characters of the desired length
     */
    private function RandomString($length = 32) {
        return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
    }

    /**
     * @Route("/stack/{page}", name="stack")
     */
    public function stack(Service\EntityMGR  $entityMGR,$page=1): Response
    {
        $qb = $entityMGR->getORM()->createQueryBuilder();
        $stacks = $entityMGR->findByPage('App:StackContent',(int) $page,20,
            $qb->expr()->isNull('q.upperID')
        );
        if(count($stacks) == 0)
            throw $this->createNotFoundException();
        return $this->render('stack/home.html.twig', [
            'stacks' => $stacks,
            'page' => $page
        ]);
    }

    /**
     * @Route("/stacknew", name="stackNew")
     */
    public function stackNew(Request $request): Response
    {
        if(is_null($this->getUser()))
            return $this->redirectToRoute('login');

        $stack = new \App\Entity\StackContent();
        $form = $this->createForm(StackNewContentType::class,$stack);
        $form->handleRequest($request);
        $guid = $this->RandomString(32);
        if ($form->isSubmitted() && $form->isValid()) {
            $stack->setView(0);
            $stack->setSubmitter($this->getUser());
            $stack->setUrl($guid);
            $stack->setDateSubmit(time());

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($stack);
            $entityManager->flush();
            return $this->redirectToRoute('stackView',['url'=>$guid,'msg'=>1]);
        }
        return $this->render('stack/newPost.html.twig', [
            'form'=>$form->createView(),
            'alert'
        ]);
    }

    /**
     * @Route("/stack/view/{url}/{msg}", name="stackView", options={"expose"=true})
     */
    public function stackView(Request $request,Service\EntityMGR  $entityMGR,$url,$msg = 0): Response
    {
        $content = $entityMGR->findOneBy('App:StackContent',['url'=>$url]);
        if(is_null($content))
            throw $this->createNotFoundException('???????? ???????? ??????');
        if($msg == 1)
            $this->addFlash('success', '???????? ?????? ?????? ????.');

        $content->setView($content->getView() + 1);
        $entityMGR->update($content);
        $stack = new \App\Entity\StackContent();
        $form = $this->createForm(StackNewReplayType::class,$stack);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $stack->setBody(str_replace('&nbsp;','',$stack->getBody()));
            if(strlen(trim(strip_tags($stack->getBody()))) >= 10){
                $stack->setView(0);
                $stack->setSubmitter($this->getUser());
                $stack->setUrl(0);
                $stack->setTitle('???????? ????:' . $content->getTitle());
                $stack->setCat($content->getCat());
                $stack->setUpperID($content->getUrl());
                $stack->setDateSubmit(time());
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($stack);
                $entityManager->flush();
                $this->addFlash('success', '???????? ?????? ?????????? ????.');
            }
            else
                $this->addFlash('danger', '?????? ?????? ???????? ?????? ?????????? ??????.?????????? ????????????????? ???? ?????????????? ????????.');

        }

        return $this->render('stack/view.html.twig', [
            'form'=>$form->createView(),
            'stack'=> $content,
            'replays'=>$entityMGR->findBy('App:StackContent',[
                'upperID'=>$content->getUrl()
            ])
        ]);
    }

    /**
     * @Route("/stack/add/like/{id}", name="stackAddLike", options={"expose"=true})
     */
    public function stackAddLike($id,Service\EntityMGR  $entityMGR): Response
    {
        if(is_null($this->getUser()))
            throw $this->createNotFoundException();
        $replay = $this->getDoctrine()->getManager()->getRepository('App:StackContent')->find($id);
        if(is_null($replay))
            throw $this->createNotFoundException();
        $replay->addLike($this->getUser());
        $this->getDoctrine()->getManager()->persist($replay);
        $this->getDoctrine()->getManager()->flush();
        return New Response('1');
    }

    /**
     * @Route("/stack-by-cat/{cat}/{page}", name="stackByCat")
     */
    public function stackByCat(Service\EntityMGR  $entityMGR, $cat,$page=1): Response
    {
        $qb = $entityMGR->getORM()->createQueryBuilder('q');
        $stacks = $entityMGR->getORM()->createQueryBuilder('q')
            ->select('q')
            ->from('App:StackContent','q')
            ->setMaxResults(20)
            ->setFirstResult(20 * ($page -1) )
            ->where($qb->expr()->isNull('q.upperID'))
            ->andWhere('q.cat = :cat')
            ->setParameter('cat',$entityMGR->findOneBy('App:StackCat',['code'=>$cat]))
            ->orderBy('q.id','DESC')
            ->getQuery()
            ->execute();

        return $this->render('stack/home.html.twig', [
            'stacks' => $stacks,
            'page' => $page
        ]);
    }
}
