<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Account;
use App\Entity\Channel;
use App\Repository\AccountRepository;
use App\Service\AccordCadreSubscription\SubscriptionMailerService;
use App\Service\AccordCadreSubscription\SubscriptionService;
use Doctrine\ORM\EntityNotFoundException;

class AccordCadreSubscriptionService
{
    public function __construct(
        private AccountRepository $accountRepository,
        private SubscriptionService $subscriptionService,
        private SubscriptionMailerService $subscriptionMailerService,
        private array $stellantisParams,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function subscription(
        array $params,
        string $accountId,
        Channel $channel,
        bool $isSendEmail = true,
    ): bool {
        $account = $this->accountRepository->find($accountId);

        if (!$account) {
            throw new EntityNotFoundException('Account not found');
        }

        $isStellantis = \in_array($params['accordId'], $this->stellantisParams['ACCORDS_IDS'], true);

        $created = $isStellantis ? $this->stellantisSubscription($account) : $this->subscriptionService->subscription($params['accordId'], $account);

        if ($created) {
            $email = $channel->getChannelParameter()?->getEmail();
            if ($isSendEmail) {
                $this->subscriptionMailerService->sendMail($account, $email, $params['accordName'], $isStellantis);
            }
        }

        return $created;
    }

    /**
     * @throws \Exception
     */
    private function stellantisSubscription(Account $account): bool
    {
        $atLeastOneCreated = false;
        foreach ($this->stellantisParams['ACCORDS_IDS'] as $id) {
            $created = $this->subscriptionService->subscription($id, $account);
            if (!$atLeastOneCreated && $created) {
                $atLeastOneCreated = true;
            }
        }

        return $atLeastOneCreated;
    }
}
