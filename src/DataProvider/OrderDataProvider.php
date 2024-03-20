<?php

declare(strict_types=1);

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Dto\Order;
use App\Entity\Account;
use App\Factory\OrderFactory;
use App\Helper\UpplerHelper;
use App\Service\UpplerOrderService;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class OrderDataProvider implements RestrictedDataProviderInterface, ItemDataProviderInterface, CollectionDataProviderInterface
{
    public function __construct(private RequestStack $requestStack, private UpplerOrderService $upplerOrderService, private OrderFactory $orderFactory)
    {
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $resourceClass === Order::class;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws \Exception
     */
    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = []): Order|null
    {
        $session = $this->requestStack->getSession();
        /** @var Account $account */
        $account = $session->get('account');

        if (!$account) {
            throw new AuthenticationException();
        }

        $remoteOrder = $this->upplerOrderService->getOrderByIdAndUserId($id, $account->getUpplerUserId());

        $orderNumber = UpplerHelper::getOrderNumber($remoteOrder);
        if ($orderNumber === null) {
            return null;
        }

        return $this->orderFactory->create($remoteOrder);
    }

    /**
     * @throws \Exception
     */
    public function getCollection(string $resourceClass, string $operationName = null): array
    {
        /** @var Account $account */
        $account = $this->requestStack->getSession()->get('account');
        $remoteOrders = $this->upplerOrderService->getOrdersByUserId($account->getUpplerUserId());
        $orders = $this->orderFactory->createAndAddToCollection($remoteOrders);

        \usort($orders, function (Order $a, Order $b) {
            return \strtotime($b->getCreatedAt()->format('Y-m-d')) - \strtotime($a->getCreatedAt()->format('Y-m-d'));
        });

        return $orders;
    }
}
