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
    \expect($account->getFirstConnectionAt())->not->toBeNull();
})->group('IntegrationDjustLogAccountServiceTest');

\it('does not overwrite first_connection_at if already set', function () {
    $existingDate = new \DateTimeImmutable('2020-01-01');
    $account = AccountFactory::createOne(['firstConnectionAt' => $existingDate]);

    $djustLogAccountService = new LogAccountConnectionService($this->entityManager);

    $djustLogAccountService->createLog($account);

    \expect($account->getFirstConnectionAt())->toEqual($existingDate);
})->group('IntegrationDjustLogAccountServiceTest');
