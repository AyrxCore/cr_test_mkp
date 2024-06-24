<?php

declare(strict_types=1);

namespace App\State\Provider;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\Order;
use App\Entity\Account;
use App\Factory\OrderFactory;
use App\Helper\UpplerHelper;
use App\Service\UpplerOrderService;
use Symfony\Component\HttpFoundation\RequestStack;

readonly class OrderProvider implements ProviderInterface
{
    public function __construct(private RequestStack $requestStack, private UpplerOrderService $upplerOrderService, private OrderFactory $orderFactory)
    {
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Exception
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        /** @var Account $account */
        $account = $this->requestStack->getSession()->get('account');

        if ($operation instanceof CollectionOperationInterface) {
            $remoteOrders = $this->upplerOrderService->getOrdersByUserId($account->getUpplerUserId());
            $orders = $this->orderFactory->createAndAddToCollection($remoteOrders);

            \usort($orders, function (Order $a, Order $b) {
                return \strtotime($b->getCreatedAt()->format('Y-m-d')) - \strtotime($a->getCreatedAt()->format('Y-m-d'));
            });

            return $orders;
        }

        $remoteOrder = $this->upplerOrderService->getOrderByIdAndUserId($uriVariables['id'], $account->getUpplerUserId());

        $orderNumber = UpplerHelper::getOrderNumber($remoteOrder);
        if ($orderNumber === null) {
            return null;
        }

        return $this->orderFactory->create($remoteOrder);
    }
}
