<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Channel;
use App\Service\SettingsService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController implements ChannelAwareControllerInterface
{
    use ChannelAwareControllerTrait;

    public function __construct(
        private RequestStack $requestStack,
        private SettingsService $settingsService,
        private LoggerInterface $logger,
    ) {
    }

    #[Route('/', name: 'prehome')]
    #[Route(
        '/{route}',
        name: 'app',
        requirements: ['route' => '^(?!.*_wdt|_profiler|login|conditions-generales-d-utilisation|mentions-legales|politique-de-confidentialite|rattachement-stellantis|rattachement-newsletter|maintenance|api).+']
    )]
    public function index(Request $request): Response
    {
        $channel = $this->getChannel($request);
        $isNeoAutoLogin = $request->cookies->get('neoAutoLogin') === 'true';

        if ($channel === null) {
            return new Response('no channel specified');
        }

        $session = $this->requestStack->getSession();

        if (
            !$session->has('account') || empty($session->get('account'))
            || !$session->has('access_token') || empty($session->get('access_token'))
        ) {
            if ($request->attributes->get('_route') !== 'prehome') {
                $path = \substr($request->getRequestUri(), 1);

                return $this->redirectToRoute('prehome', ['target' => $path]);
            }

            // Clear local cookie as user is not connected anymore
            $response = new Response();
            $response->headers->clearCookie('BEARER');
            $response->headers->clearCookie('neoAutoLogin');

            return $this->render(
                'index.html.twig',
                [
                    'channel' => $channel,
                ],
                $response
            );
        }

        return $this->render('index_app.html.twig', [
            'channel' => $channel,
            'isNeoAutoLogin' => $isNeoAutoLogin,
        ]);
    }

    #[Route('/mentions-legales', name: 'mentions_legales')]
    public function mentionsLegales(Request $request): Response
    {
        return $this->render('mentions-legales.html.twig', ['channel' => $this->requireChannel($request)]);
    }

    #[Route('/politique-de-confidentialite', name: 'politique_de_confidentialite')]
    public function politiqueConfidentialite(Request $request): Response
    {
        return $this->render('politique-de-confidentialite.html.twig', ['channel' => $this->requireChannel($request)]);
    }

    #[Route('/conditions-generales-d-utilisation', name: 'conditions_generales_d_utilisation')]
    public function conditionsGeneralesUtilisation(Request $request): Response
    {
        return $this->render('conditions-generales-d-utilisation.html.twig', ['channel' => $this->requireChannel($request)]);
    }

    #[Route('/maintenance', name: 'maintenance')]
    public function maintenance(Request $request): Response
    {
        if (!$this->settingsService->isMaintenanceMode()) {
            return $this->redirectToRoute('prehome');
        }

        return $this->render('maintenance.html.twig', ['channel' => $this->requireChannel($request)]);
    }

    private function requireChannel(Request $request): Channel
    {
        $channel = $this->getChannel($request);
        if ($channel === null) {
            $this->logger->warning('No channel found for hostname, returning 404', [
                'host' => $request->headers->get('host'),
                'uri' => $request->getRequestUri(),
            ]);
            throw $this->createNotFoundException();
        }

        return $channel;
    }
}
