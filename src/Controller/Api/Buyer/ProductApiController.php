<?php

declare(strict_types=1);

namespace App\Controller\Api\Buyer;

use App\Context\ChannelContext;
use App\Dto\AccountAccordCadre;
use App\Repository\AccountRepository;
use App\Service\AccordCadreSubscriptionService;
use App\Service\RequestContactMailerService;
use App\Service\UpplerProductService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ProductApiController extends AbstractController
{
    public const int DEFAULT_PAGE_NUMBER = 1;
    public const int DEFAULT_PER_PAGE = 5;

    public function __construct(
        private RequestStack $requestStack,
        private UpplerProductService $upplerProductService,
        private AccordCadreSubscriptionService $accordCadreSubscriptionService,
        private RequestContactMailerService $requestContactMailerService,
        private readonly AccountRepository $accountRepository,
    ) {
    }

    #[Route('/api/variant/{id}', name: 'get_variant')]
    public function variant(int $id): JsonResponse
    {
        $variant = $this->upplerProductService->findVariantById($id);

        return new JsonResponse($variant);
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

        $data = $request->request->all();

        if (!isset($data['accordId'], $data['accordName'])) {
            throw new BadRequestHttpException('Missing required parameters.');
        }

        $subscriptionParams = [
            'accordId' => $data['accordId'],
            'accordName' => $data['accordName'],
        ];

        $this->accordCadreSubscriptionService->subscription($subscriptionParams, $accountId, $channelContext->getChannel());

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
