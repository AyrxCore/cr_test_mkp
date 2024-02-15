<?php

declare(strict_types=1);

namespace App\Controller\Api\Buyer;

use App\Context\ChannelContext;
use App\Service\AccordCadreSubscriptionService;
use App\Service\UpplerProductService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ProductApiController extends AbstractController
{
    public const DEFAULT_PAGE_NUMBER = 1;
    public const DEFAULT_PER_PAGE = 5;

    public function __construct(
        private RequestStack $requestStack,
        private UpplerProductService $upplerProductService,
        private AccordCadreSubscriptionService $accordCadreSubscriptionService,
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
    #[Route('/api/accord-cadre-subscription', name: 'accord_cadre_subscription', methods: ['POST'])]
    public function subscription(
        Request $request,
        ChannelContext $channelContext
    ): JsonResponse {
        $session = $this->requestStack->getSession();

        if (
            !$session->has('account') || empty($session->get('account'))
            || !$session->has('access_token') || empty($session->get('access_token'))
        ) {
            throw new AccessDeniedException('Access denied to make subscription. Please login.');
        }
        $accountId = (string) $session->get('account')->getId();

        $params = \json_decode($request->getContent(), true);
        if (!isset($params['accordId'], $params['accordName'])) {
            throw new BadRequestHttpException('Missing required parameters.');
        }

        return new JsonResponse($this->accordCadreSubscriptionService->subscription($params, $accountId, $channelContext));
    }
}
