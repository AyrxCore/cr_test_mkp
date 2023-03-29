<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Service\Attribute\Required;

class DefaultController extends AbstractController
{
    #[Required]
    public RequestStack $requestStack;

    #[Route('/', name: 'prehome')]
    #[Route('/{route}', name: 'app', requirements: ['route' => '^(?!.*_wdt|_profiler|login|api).+'])]
    public function index(Request $request): Response
    {
        $session = $this->requestStack->getSession();
        $session->start();

        if (!$session->has('account') || empty($session->get('account'))) {
            if ($request->attributes->get('_route') !== 'prehome') {
                return $this->redirectToRoute('prehome');
            }
            return $this->render('index.html.twig');
        }
        return $this->render('index_app.html.twig');
    }
}
