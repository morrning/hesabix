<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BuyController extends AbstractController
{
    #[Route('/app/user/remove/ads', name: 'app_remove_ads')]
    public function app_remove_ads(): Response
    {
        if($this->getUser()->getAdsBan())
            return $this->redirectToRoute('app_user_profile');
        return $this->render('buy/buy.html.twig', [

        ]);
    }
}
