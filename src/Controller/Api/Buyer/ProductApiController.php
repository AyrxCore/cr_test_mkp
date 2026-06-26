<?php

declare(strict_types=1);

namespace App\Controller\Api\Buyer;

use App\Context\ChannelContext;
use App\Dto\AccountAccordCadre;
use App\Repository\AccordRepository;
use App\Repository\AccountRepository;
use App\Service\AccordCadreSubscriptionService;
use App\Service\RequestContactMailerService;
use App\Service\UpplerProductService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

class ProductApiController extends AbstractController
{
    public function __construct(
        private RequestStack $requestStack,
        private UpplerProductService $upplerProductService,
        private AccordCadreSubscriptionService $accordCadreSubscriptionService,
        private RequestContactMailerService $requestContactMailerService,
        private readonly AccountRepository $accountRepository,
        private readonly AccordRepository $accordRepository,
    ) {
    }

    /**
     * @throws \Exception
     */
    #[Route('/api/not-sellable-contact-request', name: 'not_sellable_contact_request', methods: ['POST'])]
    public function notSellableContactRequest(
        Request $request,
        ChannelContext $channelContext,
    ): JsonResponse {
        $session = $this->requestStack->getSession();

        $accountId = (string) $session->get('account')->getId();

        $data = \json_decode($request->getContent(), true);

        if ($data === null) {
            throw new BadRequestHttpException('Invalid JSON data.');
        }

        if (isset($data['accordId'])) {
            if (!Uuid::isValid($data['accordId'])) {
                throw new BadRequestHttpException('Invalid accord ID format.');
            }

            $accord = $this->accordRepository->find(Uuid::fromString($data['accordId']));
            if ($accord === null) {
                throw new BadRequestException(\sprintf('Accord with ID %s not found.', $data['accordId']));
            }

            $data['accordName'] = $accord->getName();

            // Souscrire à l'accord-cadre
            $subscriptionParams = [
                'accordId' => $data['accordId'],
                'accordName' => $data['accordName'],
            ];

            $this->accordCadreSubscriptionService->subscription($subscriptionParams, $accountId, $channelContext->getChannel());
        }

        $account = $this->accountRepository->findOneBy(['id' => $accountId]);
        $email = $channelContext->getChannel()->getChannelParameter()?->getEmail();
        $this->requestContactMailerService->sendMail($account, $email, $data);

        return new JsonResponse();
    }

    /**
     * @throws \Exception
     */
    #[Route('/api/accord-cadre-subscription', name: 'accord_cadre_subscription', methods: ['POST'])]
    public function subscription(
        Request $request,
        ChannelContext $channelContext,
    ): JsonResponse {
        $session = $this->requestStack->getSession();

        $accountId = (string) $session->get('account')->getId();

        $params = \json_decode($request->getContent(), true);
        if (!isset($params['accordId'], $params['accordName'])) {
            throw new BadRequestHttpException('Missing required parameters.');
        }

        $created = $this->accordCadreSubscriptionService->subscription($params, $accountId, $channelContext->getChannel());

        if (!$created) {
            throw new BadRequestHttpException();
        }

        return new JsonResponse(AccountAccordCadre::PROCESS_STATUS_PENDING);
    }
}
