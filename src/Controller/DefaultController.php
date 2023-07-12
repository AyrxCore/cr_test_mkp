<?php

namespace App\Controller;

use App\Repository\SettingRepository;
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

    #[Required]
    public SettingRepository $settingRepository;

    #[Route('/', name: 'prehome')]
    #[Route(
        '/{route}',
        name: 'app',
        requirements: ['route' => '^(?!.*_wdt|_profiler|login|mentions-legales|politique-de-confidentialite|maintenance|api).+']
    )
    ]
    public function index(Request $request): Response
    {
        $session = $this->requestStack->getSession();

        if (
            !$session->has('account') || empty($session->get('account'))
            || !$session->has('access_token') || empty($session->get('access_token'))
        ) {
            if ($request->attributes->get('_route') !== 'prehome') {
                $path = substr($request->getRequestUri(), 1);
                return $this->redirectToRoute('prehome', ['target' => $path]);
            }
            return $this->render('index.html.twig');
        }
        return $this->render('index_app.html.twig');
    }

    #[Route('/mentions-legales', name: 'mentions_legales')]
    public function mentionsLegales(): Response
    {
        return $this->render('mentions-legales.html.twig');
    }

    #[Route('/politique-de-confidentialite', name: 'politique_de_confidentialite')]
    public function politiqueConfidentialite(): Response
    {
        return $this->render('politique-de-confidentialite.html.twig');
    }

    #[Route('/maintenance', name: 'maintenance')]
    public function maintenance(): Response
    {
        $maintenanceMode = $this->settingRepository->findOneBy(['name' => 'maintenance']);

        if (is_null($maintenanceMode) || (int)$maintenanceMode->getValue() !== 1) {
            return $this->redirectToRoute('prehome');
        }

        return $this->render('maintenance.html.twig');
    }
}
