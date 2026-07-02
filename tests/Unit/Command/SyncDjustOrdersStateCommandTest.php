<?php

declare(strict_types=1);

use App\Command\SyncDjustOrdersStateCommand;
use App\Service\Djust\DjustOrdersSyncService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

\uses()->group('UnitSyncDjustOrdersStateCommand', 'UnitCommand', 'Djust');

\beforeEach(function () {
    $this->djustOrdersSyncService = Mockery::mock(DjustOrdersSyncService::class);
    $this->command = new SyncDjustOrdersStateCommand($this->djustOrdersSyncService);
    $this->tester = new CommandTester($this->command);
});

\afterEach(function () {
    Mockery::close();
});

\it('displays success message with sync stats', function () {
    $this->djustOrdersSyncService
        ->shouldReceive('sync')
        ->once()
        ->andReturn(['processed' => 3, 'updated' => 1, 'skipped' => 1, 'failed' => 0]);

    $exitCode = $this->tester->execute([]);

    \expect($exitCode)->toBe(Command::SUCCESS);
    \expect($this->tester->getDisplay())->toContain('processed=3 updated=1 skipped=1 failed=0');
});

\it('returns failure exit code when sync reports failures', function () {
    $this->djustOrdersSyncService
        ->shouldReceive('sync')
        ->once()
        ->andReturn(['processed' => 2, 'updated' => 0, 'skipped' => 1, 'failed' => 1]);

    $exitCode = $this->tester->execute([]);

    \expect($exitCode)->toBe(Command::FAILURE);
    \expect($this->tester->getDisplay())->toContain('processed=2 updated=0 skipped=1 failed=1');
});
