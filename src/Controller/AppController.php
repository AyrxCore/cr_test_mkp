<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[Route("/app")]
class AppController extends AbstractController
{
    #[Route('/{param1?}/{param2?}/{param3?}', name: 'app_link')]
    public function index(): Response
    {
        return $this->render('index_app.html.twig');
    }
}
