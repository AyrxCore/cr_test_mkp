<?php

declare(strict_types=1);

namespace App\Service\Djust;

use App\Entity\CartSavings;
use App\Repository\CartSavingsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\Exception\ClientException;

class DjustOrdersSyncService
{
    private const int BATCH_SIZE = 50;

    public function __construct(
        private readonly CartSavingsRepository $cartSavingsRepository,
        private readonly DjustOrderService $djustOrderService,
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * @return array{processed: int, updated: int, skipped: int, failed: int}
     */
    public function sync(): array
    {
        $processed = 0;
        $updated = 0;
        $skipped = 0;
        $failed = 0;

        /** @var CartSavings $saving */
        foreach ($this->cartSavingsRepository->iterateWithOrderId() as $saving) {
            ++$processed;

            $orderId = $saving->getOrderId();
            if ($orderId === null || $orderId === '') {
                ++$skipped;
                continue;
            }

            try {
                $order = $this->djustOrderService->getOrderByIdForAccount($orderId, $saving->getAccount());

                if ($order === null) {
                    ++$skipped;
                    $this->logger->warning('Djust order not found during analytics sync.', [
                        'orderId' => $orderId,
                        'cartSavingsId' => $saving->getId()?->toRfc4122(),
                    ]);
                    continue;
                }

                $orderStatus = $order['orderLogistics'][0]['status'] ?? $order['status'] ?? null;

                if ($saving->getOrderState() === $orderStatus) {
                    ++$skipped;
                    continue;
                }

                $saving->setOrderState($orderStatus);
                $this->cartSavingsRepository->save($saving);
                ++$updated;

                if ($updated % self::BATCH_SIZE === 0) {
                    $this->entityManager->flush();
                }
            } catch (ClientException $clientException) {
                if ($clientException->getCode() === 404) {
                    ++$skipped;
                    $this->logger->warning('Djust order not found via operator during analytics sync (404).', [
                        'orderId' => $orderId,
                        'cartSavingsId' => $saving->getId()?->toRfc4122(),
                    ]);
                    continue;
                }

                ++$failed;
                $this->logger->error('Unable to sync Djust order state.', [
                    'orderId' => $orderId,
                    'cartSavingsId' => $saving->getId()?->toRfc4122(),
                    'exception' => $clientException,
                ]);
            } catch (\Throwable $throwable) {
                ++$failed;
                $this->logger->error('Unable to sync Djust order state.', [
                    'orderId' => $orderId,
                    'cartSavingsId' => $saving->getId()?->toRfc4122(),
                    'exception' => $throwable,
                ]);
            }
        }

        $this->entityManager->flush();

        return [
            'processed' => $processed,
            'updated' => $updated,
            'skipped' => $skipped,
            'failed' => $failed,
        ];
    }
}
