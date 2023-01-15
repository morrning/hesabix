<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GeneralController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function home(EntityManagerInterface $entityManager): Response
    {
        if($this->getUser())
            return $this->redirectToRoute('app_main');
        return $this->render('general/home.html.twig', [
            'stat' => [
                'users'=>$entityManager->getRepository(\App\Entity\User::class)->count([]),
                'bid'=>$entityManager->getRepository(\App\Entity\Business::class)->count([]),
                'doc'=>$entityManager->getRepository(\App\Entity\HesabdariFile::class)->count([]),
                'com'=>$entityManager->getRepository(\App\Entity\Commodity::class)->count([]),
                'lastbid'=>$entityManager->getRepository(\App\Entity\Business::class)->findLast()
            ],
        ]);
    }
    #[Route('/faq', name: 'homeFaq')]
    public function homeFaq(): Response
    {
        return $this->render('general/faq.html.twig', [
        ]);
    }
    #[Route('/prices', name: 'homePrices')]
    public function homePrices(): Response
    {
        return $this->render('general/prices.html.twig', [
            'controller_name' => 'GeneralController',
        ]);
    }
    #[Route('/about', name: 'homeAbout')]
    public function homeAbout(): Response
    {
        return $this->render('general/about.html.twig', [
            'controller_name' => 'GeneralController',
        ]);
    }

    #[Route('/contact-us', name: 'homeContactus')]
    public function homeContactus(): Response
    {
        return $this->render('general/contact.html.twig', []);
    }
}
