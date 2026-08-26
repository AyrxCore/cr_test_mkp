<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\LogAutoLoginError;
use App\Repository\LogAutoLoginErrorRepository;
use Psr\Log\LoggerInterface;

class LogAutoLoginErrorService
{
    public function __construct(
        private readonly LogAutoLoginErrorRepository $logAutoLoginExceptionRepository,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function log(?string $channelName, string $email, string $reason): void
    {
        $logAutoLoginException = new LogAutoLoginError();
        $logAutoLoginException->setChannelName($channelName);
        $logAutoLoginException->setEmail($email);
        $logAutoLoginException->setReason($reason);

        $this->logAutoLoginExceptionRepository->save($logAutoLoginException);

        $this->logger->warning('AutoLogin failed', ['channel' => $channelName, 'email' => $email, 'reason' => $reason]);
    }
}
