<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\CartSavingsRepository;
use App\Service\UpplerOrderService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:retrieve-orders-state',
    description: 'Retrieve the state of orders from Uppler'
)]
class RetrieveOrdersStateCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UpplerOrderService $upplerOrderService,
        private readonly CartSavingsRepository $cartSavingsRepository
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $savings = $this->cartSavingsRepository->findAll();

        foreach ($savings as $saving) {
            try {
                $order = $this->upplerOrderService->getOrderByIdAsAdmin((int) $saving->getOrderId());
                $saving->setOrderState($order['state']);
                $this->cartSavingsRepository->save($saving);
            } catch (\Exception $e) {
                continue;
            }
        }

        $this->entityManager->flush();

        return Command::SUCCESS;
    }
}
