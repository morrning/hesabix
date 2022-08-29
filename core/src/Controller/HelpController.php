<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HelpController extends AbstractController
{
    #[Route('/help/{id}', name: 'app_help')]
    public function app_help($id, EntityManagerInterface $entityManager): Response
    {
        $topic = $entityManager->getRepository('App:HelpTopics')->findOneBy(['url'=>$id]);
        if(!$topic)
            throw $this->createNotFoundException();
        return $this->render('help/index.html.twig', [
            'topic' => $topic,
        ]);
    }
}
