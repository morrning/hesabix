<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PersonController extends AbstractController
{
    #[Route('/api/person/recive/base', name: 'app_person_recive_base')]
    public function app_person_recive_base(): Response
    {
        return $this->json(['Data' => 'OK']);
    }
}
