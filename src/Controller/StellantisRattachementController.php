<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Channel;
use App\Repository\AccountRepository;
use App\Service\AccordCadreSubscriptionService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
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
        private LoggerInterface $logger,
        private AccountRepository $accountRepository,
        private AccordCadreSubscriptionService $subscriptionService,
        private ParameterBagInterface $parameterBag
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

        $accords = $this->parameterBag->get('STELLANTIS_PARAMS')['ACCORDS_IDS'];

        foreach ($accounts as $account) {
            \shuffle($accords);
            $params = [
                'accordId' => $accords[0],
                'accordName' => 'Stellantis',
            ];
            $this->subscriptionService->subscription($params, $account->getId()->__toString(), $channel);
        }

        return $this->render('stellantis/index.html.twig', [
            'channel' => $channel,
        ]);
    }

    private function renderError(string $errorMessage, Channel $channel): Response
    {
        return $this->render('stellantis/error.html.twig', [
            'errorMessage' => $errorMessage,
            'channel' => $channel,
        ]);
    }
}
