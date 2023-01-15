<?php

namespace App\Controller;

use App\Entity\HelpTopics;
use App\Form\HelpType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HelpController extends AbstractController
{
    #[Route('/admin/help-new', name: 'app_help_new')]
    public function app_help_new(EntityManagerInterface $entityManager,Request $request): Response
    {
        $help = new HelpTopics();
        return $this->extracted($help, $request, $entityManager);
    }

    #[Route('/admin/help-edit/{id}', name: 'app_help_edit')]
    public function app_help_edit($id,EntityManagerInterface $entityManager,Request $request): Response
    {
        $help = $entityManager->getRepository(\App\Entity\HelpTopics::class)->find($id);
        if(!$help)
            throw $this->createNotFoundException();
        return $this->extracted($help, $request, $entityManager);
    }

    #[Route('/help-by-cat/{id}', name: 'app_help_cat')]
    public function app_help_cat($id, EntityManagerInterface $entityManager): Response
    {
        $cat = $entityManager->find(\App\Entity\HelpCat::class,$id);
        if(!$cat)
            throw $this->createNotFoundException();

        $topics = $entityManager->getRepository(\App\Entity\HelpTopics::class)->findBy(['cat'=>$cat]);

        return $this->render('help/list.html.twig', [
            'topics' => $topics,
            'cat'=>$cat
        ]);
    }
    #[Route('/admin/help-delete/{id}', name: 'app_help_delete')]
    public function app_help_delete($id, EntityManagerInterface $entityManager): Response
    {
        $topic = $entityManager->find(\App\Entity\HelpTopics::class,$id);
        if($topic){
            $entityManager->remove($topic);
            $entityManager->flush();
        }
        return $this->redirectToRoute('app_help',['id'=>'home']);
    }


    #[Route('/help/{id}', name: 'app_help')]
    public function app_help($id, EntityManagerInterface $entityManager): Response
    {
        $topic = $entityManager->getRepository(\App\Entity\HelpTopics::class)->findOneBy(['url'=>$id]);
        if(!$topic)
            throw $this->createNotFoundException();
        return $this->render('help/content.html.twig', [
            'topic' => $topic,
        ]);
    }

    /**
     * @param mixed $help
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function extracted(mixed $help, Request $request, EntityManagerInterface $entityManager): Response|\Symfony\Component\HttpFoundation\RedirectResponse
    {
        $form = $this->createForm(HelpType::class, $help, []);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($help);
            $entityManager->flush();
            return $this->redirectToRoute('app_help', ['id' => $help->getUrl()]);
        }
        return $this->render('help/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
