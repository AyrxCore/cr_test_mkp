<?php

declare(strict_types=1);

use App\Repository\LogAutoLoginErrorRepository;
use App\Service\LogAutoLoginErrorService;
use Faker\Factory;

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

    $logAutoLoginErrorService = new LogAutoLoginErrorService($logAutoLoginErrorRepository);

    $logAutoLoginErrorService->log($channelName, $email, $reason);
})->group('LogAutoLoginErrorService');
