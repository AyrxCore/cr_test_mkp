<?php

declare(strict_types=1);

use App\Repository\LogAutoLoginErrorRepository;
use App\Service\LogAutoLoginErrorService;
use Faker\Factory;
use Psr\Log\LoggerInterface;

\it('creates and saves LogAutoLoginErrorCorrectly', function () {
    $channelName = Factory::create()->word();
    $email = Factory::create()->email();
    $reason = Factory::create()->sentence();

    $logAutoLoginErrorRepository = Mockery::mock(LogAutoLoginErrorRepository::class);
    $logAutoLoginErrorRepository->shouldReceive('save')
        ->once()
        ->withArgs(function ($logAutoLoginError) use ($channelName, $email, $reason) {
            return $logAutoLoginError->getChannelName() === $channelName
                && $logAutoLoginError->getEmail() === $email
                && $logAutoLoginError->getReason() === $reason;
        });

    $logger = Mockery::mock(LoggerInterface::class);
    $logger->shouldReceive('warning')
        ->once()
        ->with('AutoLogin failed', ['channel' => $channelName, 'email' => $email, 'reason' => $reason]);

    $logAutoLoginErrorService = new LogAutoLoginErrorService($logAutoLoginErrorRepository, $logger);

    $logAutoLoginErrorService->log($channelName, $email, $reason);
})->group('LogAutoLoginErrorService');
