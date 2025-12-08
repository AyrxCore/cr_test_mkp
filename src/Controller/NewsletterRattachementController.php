<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Channel;
use App\Repository\AccountRepository;
use App\Repository\PartnerRepository;
use App\Service\AccordCadreSubscriptionService;
use App\Service\MailerProvider;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;

use function Sentry\captureMessage;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Twig\Environment;

class NewsletterRattachementController extends AbstractController implements ChannelAwareControllerInterface
{
    use ChannelAwareControllerTrait;

    private const string CACHE_NAME = 'allow_unique_link';

    public function __construct(
        private readonly AccountRepository $accountRepository,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly LoggerInterface $logger,
        private readonly RequestStack $requestStack,
        private readonly AccordCadreSubscriptionService $accordCadreSubscriptionService,
        private readonly PartnerRepository $partnerRepository,
        private readonly MailerProvider $mailerProvider,
        private readonly Environment $twig,
        private readonly CacheInterface $cache,
    ) {
    }

    /**
     * @throws \Exception
     */
    #[Route('/rattachement-newsletter', name: 'rattachement_newsletter', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $channel = $this->getChannel($request);
        $email = $request->get('email') ?? null;
        $partnerId = $request->get('partnerId') ?? null;

        if (\filter_var($email, \FILTER_VALIDATE_EMAIL) === false) {
            return $this->renderError('L\'adresse email '.$email.'est invalide', $channel);
        }

        try {
            $partner = $this->partnerRepository->find($partnerId);
            if (!$partner) {
                throw new \Exception();
            }
        } catch (\Throwable $e) {
            return $this->renderError('Fournisseur non identifié', $channel);
        }

        return $this->render('newsletter/loading.html.twig', [
            'channel' => $channel,
            'partnerName' => $partner->getName(),
            'email' => $email,
            'partnerId' => $partnerId,
        ]);
    }

    #[Route('/rattachement-newsletter/process', name: 'rattachement_newsletter_process', methods: ['POST'])]
    public function process(Request $request): Response
    {
        $channel = $this->getChannel($request);
        $email = $request->get('email') ?? null;
        $partnerId = $request->get('partnerId') ?? null;
        $host = $request->headers->get('host', '');

        if (\filter_var($email, \FILTER_VALIDATE_EMAIL) === false) {
            return $this->renderError('L\'adresse email '.$email.'est invalide', $channel);
        }

        try {
            $partner = $this->partnerRepository->find($partnerId);
            if (!$partner) {
                throw new \Exception();
            }
        } catch (\Throwable $e) {
            return $this->renderError('Fournisseur non identifié', $channel);
        }

        $accounts = $this->accountRepository->findAccountsByUserEmailAndChannelCode($email, $channel->getCode());
        if (empty($accounts)) {
            return $this->renderError('Aucun compte n\'a été trouvé pour le mail '.$email, $channel);
        }

        $cacheAllowUniqueLink = $this->cache->getItem(self::CACHE_NAME);
        $cacheValues = $cacheAllowUniqueLink->get();

        foreach ($accounts as $account) {
            $adherent = $account->getAdherent();
            $service = $adherent->getChannel()->getCode() === 'QANTIS_ACHAT' ? 'QANTIS' : $adherent->getRootParent()->getName();

            if (!$cacheValues
                || !(($cacheValues['partnerId'] ?? null) === $partnerId)
                || !(($cacheValues['email'] ?? null) === $email)
                || !(($cacheValues['host'] ?? null) === $host)
            ) {
                if (empty($partner->getRattachementRecipients())) {
                    captureMessage(\sprintf('Aucun destinataire de rattachement renseigné pour le partenaire %s', $partner->getId()));
                } else {
                    $this->mailerProvider->send(
                        $channel->getChannelParameter()->getEmail(),
                        $partner->getRattachementRecipients(),
                        'Demande de rattachement à l\'accord-cadre '.$partner->getName(),
                        $this->twig->render('mails/request.newsletter.accord.subscription.html.twig', [
                            'partnerName' => $partner->getName(),
                            'adherent' => $adherent,
                            'account' => $account,
                            'service' => $service,
                        ])
                    );
                }
            }

            foreach ($partner->getAccords() as $accord) {
                try {
                    $params = ['accordId' => (string) $accord->getId(), 'accordName' => $accord->getName()];
                    $this->accordCadreSubscriptionService->subscription($params, (string) $account->getId(), $channel, isSendEmail: false);
                } catch (\Exception $e) {
                    $this->logger->error('Error processing newsletter subscription: '.$e->getMessage());
                    captureMessage(\sprintf('Erreur lors du rattachement via newsletter pour l\'account %s  : '.$e->getMessage(), $account->getId()));

                    return $this->renderError('Une erreur est survenue lors du rattachement', $channel);
                }
            }
        }

        $cacheItem = $this->cache->getItem(self::CACHE_NAME);
        $cacheItem->set(['partnerId' => $partnerId, 'email' => $email, 'host' => $host]);
        $this->cache->save($cacheItem);

        return $this->redirectToRoute('rattachement_newsletter_success', [
            'partnerName' => $partner->getName(),
        ]);
    }

    #[Route('/rattachement-newsletter/success', name: 'rattachement_newsletter_success', methods: ['GET'])]
    public function success(Request $request): Response
    {
        $channel = $this->getChannel($request);
        $partnerName = $request->query->get('partnerName', '');

        return $this->render('newsletter/index.html.twig', [
            'channel' => $channel,
            'partnerName' => $partnerName,
        ]);
    }

    private function renderError(string $errorMessage, Channel $channel): Response
    {
        return $this->render('newsletter/error.html.twig', [
            'errorMessage' => $errorMessage,
            'channel' => $channel,
        ]);
    }
}
