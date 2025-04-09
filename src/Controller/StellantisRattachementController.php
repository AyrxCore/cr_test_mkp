<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Channel;
use App\Events\UserAcceptStellantisModalEvent;
use App\Repository\AccountRepository;
use App\Service\StellantisService;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StellantisRattachementController extends AbstractController implements ChannelAwareControllerInterface
{
    use ChannelAwareControllerTrait;

    public const array ALLOWED_CHANNELS = [
        Channel::ARTEMA,
        Channel::DLR,
        Channel::FNPHP,
        Channel::FSPF,
        Channel::QANTIS_ACHAT,
        Channel::SYNETAM,
        Channel::UITS,
        Channel::UNEP,
        Channel::UNGE,
    ];

    public function __construct(
        private readonly AccountRepository $accountRepository,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly LoggerInterface $logger,
        private readonly RequestStack $requestStack,
        private readonly StellantisService $stellantisService,
    ) {
    }

    /**
     * @throws \Exception
     */
    #[Route('/rattachement-stellantis', name: 'rattachement_stellantis', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $channel = $this->getChannel($request);
        if (!\in_array($channel?->getCode(), self::ALLOWED_CHANNELS, true)) {
            return $this->redirectToRoute('prehome');
        }

        $email = $request->get('email') ?? null;
        if (\filter_var($email, \FILTER_VALIDATE_EMAIL) === false) {
            return $this->renderError('L\'adresse email '.$email.' est invalide', $channel);
        }

        $accounts = $this->accountRepository->findAccountsByUserEmailAndChannelCode($email, $channel->getCode());
        if (empty($accounts)) {
            return $this->renderError('Aucun compte n\'a été trouvé pour le mail '.$email, $channel);
        }

        foreach ($accounts as $account) {
            try {
                $this->stellantisService->processStellantisSubscription($account);
            } catch (\Exception $e) {
                $this->logger->error('Error processing Stellantis subscription: '.$e->getMessage());

                return $this->renderError('Une erreur est survenue lors du rattachement', $channel);
            }
        }

        return $this->render('stellantis/index.html.twig', [
            'channel' => $channel,
        ]);
    }

    #[Route('/api/stellantis-subscription', name: 'stellantis_api_subscription', methods: ['POST'])]
    public function subscription(): JsonResponse
    {
        $session = $this->requestStack->getSession();
        $account = $this->accountRepository->find($session->get('account')->getId());

        $this->eventDispatcher->dispatch(new UserAcceptStellantisModalEvent($account));

        return new JsonResponse(
            [
                'status' => 'ok',
                'message' => 'Subscription request sent successfully.',
            ]
        );
    }

    private function renderError(string $errorMessage, Channel $channel): Response
    {
        return $this->render('stellantis/error.html.twig', [
            'errorMessage' => $errorMessage,
            'channel' => $channel,
        ]);
    }
}
