<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\LogAutoLoginError;
use App\Repository\LogAutoLoginErrorRepository;

class LogAutoLoginErrorService
{
    public function __construct(private readonly LogAutoLoginErrorRepository $logAutoLoginExceptionRepository)
    {
    }

    public function log(?string $channelName, string $email, string $reason): void
    {
        $logAutoLoginException = new LogAutoLoginError();
        $logAutoLoginException->setChannelName($channelName);
        $logAutoLoginException->setEmail($email);
        $logAutoLoginException->setReason($reason);

        $this->logAutoLoginExceptionRepository->save($logAutoLoginException);
    }
}
