<?php

declare(strict_types=1);

use App\DataFixtures\Factory\AccountFactory;
use App\Entity\LogAccountConnection;
use App\Service\LogAccountConnectionService;

\it('save log account connection', function () {
    $account = AccountFactory::createOne();

    $djustLogAccountService = new LogAccountConnectionService($this->entityManager);

    $djustLogAccountService->createLog($account);

    $logAccountConnectionRepository = $this->entityManager->getRepository(LogAccountConnection::class);

    \expect($logAccountConnectionRepository->findOneBy(['account' => $account]))->not->toBeNull();
})->group('IntegrationDjustLogAccountServiceTest');
