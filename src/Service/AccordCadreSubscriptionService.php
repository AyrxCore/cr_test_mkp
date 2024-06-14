<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\AccountAccordCadre;
use App\Entity\Account;
use App\Entity\Channel;
use App\Repository\AccountRepository;
use App\Service\AccordCadreSubscription\SubscriptionMailerService;
use App\Service\AccordCadreSubscription\SubscriptionService;
use Doctrine\ORM\EntityNotFoundException;
use Exception;

class AccordCadreSubscriptionService
{
    public function __construct(
        private AccountRepository $accountRepository,
        private SubscriptionService $subscriptionService,
        private SubscriptionMailerService $subscriptionMailerService,
        private array $stellantisParams
    ) {
    }

    /**
     * @throws Exception
     */
    public function subscription(
        array $params,
        string $accountId,
        Channel $channel,
    ): string {
        $account = $this->accountRepository->find($accountId);

        if (!$account) {
            throw new EntityNotFoundException('Account not found');
        }

        $isStellantis = in_array($params['accordId'], $this->stellantisParams['ACCORDS_IDS']);

        $status = $isStellantis ? $this->stellantisSubscription($account) : $this->subscriptionService->subscription($params['accordId'], $account);

        if ($status === AccountAccordCadre::PROCESS_STATUS_PENDING) {
            $email = $channel->getChannelParameter()?->getEmail();
            $this->subscriptionMailerService->sendMail($account, $email, $params['accordName'], $isStellantis);
        }

        return $status;
    }

    /**
     * @throws Exception
     */
    private function stellantisSubscription(Account $account): string
    {
        $atLeastOneStatusPending = null;
        foreach ($this->stellantisParams['ACCORDS_IDS'] as $id) {
            $status = $this->subscriptionService->subscription($id, $account);
            if (!$atLeastOneStatusPending && $status === AccountAccordCadre::PROCESS_STATUS_PENDING) {
                $atLeastOneStatusPending = $status;
            }
        }
        return $atLeastOneStatusPending;
    }
}
