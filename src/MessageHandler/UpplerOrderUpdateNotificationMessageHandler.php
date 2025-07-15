<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\UpplerOrderUpdateNotificationMessage;
use App\Repository\CartSavingsRepository;
use App\Service\UpplerOrderService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;

#[AsMessageHandler]
readonly class UpplerOrderUpdateNotificationMessageHandler
{
    public function __construct(
        private CartSavingsRepository $cartSavingsRepository,
        private EntityManagerInterface $entityManager,
        private UpplerOrderService $upplerOrderService,
    ) {
    }

    public function __invoke(UpplerOrderUpdateNotificationMessage $message): void
    {
        try {
            $orderId = $message->getOrderId();

            $order = $this->upplerOrderService->getOrderByIdAsAdmin($orderId);

            if (!$saving = $this->cartSavingsRepository->findOneByOrderId($order['id'])) {
                throw new EntityNotFoundException('CartSavings not found for order ID: '.$order['id']);
            }
            $saving->setOrderState($order['state']);
            $this->entityManager->persist($saving);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            throw new UnrecoverableMessageHandlingException($e->getMessage(), 0, $e);
        }
    }
}
