<?php

declare(strict_types=1);

use App\DataFixtures\Factory\ChannelFactory;
use App\DataFixtures\Factory\ChannelOptionFactory;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

\uses()->group('IntegrationCleanObsoleteChannelOptionsCommand');

\beforeEach(function () {
    $this->application = new Application(static::$kernel);
});

\it('removes obsolete HOMEPAGE_ACCORD_CADRE_CHANNEL_PROPERTY_VALUE_ID options', function () {
    // Create test channel and option
    $channel = ChannelFactory::createOne([
        'code' => 'TEST_CHANNEL',
        'name' => 'Test Channel',
        'hostname' => 'test.example.com',
    ]);

    ChannelOptionFactory::createOne([
        'channel' => $channel,
        'name' => 'HOMEPAGE_ACCORD_CADRE_CHANNEL_PROPERTY_VALUE_ID',
        'value' => 'test_value',
    ]);

    // Run command
    $command = $this->application->find('channel:clean-obsolete-options');
    $commandTester = new CommandTester($command);
    $commandTester->execute([
        'option-names' => ['HOMEPAGE_ACCORD_CADRE_CHANNEL_PROPERTY_VALUE_ID'],
    ]);

    // Check output
    $output = $commandTester->getDisplay();
    \expect($output)->toContain('1 option(s) obsolète(s) supprimée(s) avec succès');

    // Verify option was deleted
    \expect(ChannelOptionFactory::repository()
        ->findOneBy(['name' => 'HOMEPAGE_ACCORD_CADRE_CHANNEL_PROPERTY_VALUE_ID'])
    )->toBeNull();
});

\it('removes obsolete HOMEPAGE_PRODUCTS_SELECTION_CHANNEL_PROPERTY_VALUE_ID options', function () {
    // Create test channel and option
    $channel = ChannelFactory::createOne([
        'code' => 'TEST_CHANNEL_2',
        'name' => 'Test Channel 2',
        'hostname' => 'test2.example.com',
    ]);

    ChannelOptionFactory::createOne([
        'channel' => $channel,
        'name' => 'HOMEPAGE_PRODUCTS_SELECTION_CHANNEL_PROPERTY_VALUE_ID',
        'value' => 'test_value_2',
    ]);

    // Run command
    $command = $this->application->find('channel:clean-obsolete-options');
    $commandTester = new CommandTester($command);
    $commandTester->execute([
        'option-names' => ['HOMEPAGE_PRODUCTS_SELECTION_CHANNEL_PROPERTY_VALUE_ID'],
    ]);

    // Check output
    $output = $commandTester->getDisplay();
    \expect($output)->toContain('1 option(s) obsolète(s) supprimée(s) avec succès');

    // Verify option was deleted
    \expect(ChannelOptionFactory::repository()
        ->findOneBy(['name' => 'HOMEPAGE_PRODUCTS_SELECTION_CHANNEL_PROPERTY_VALUE_ID'])
    )->toBeNull();
});

\it('removes multiple obsolete options across multiple channels', function () {
    // Create test channels and options
    $channel1 = ChannelFactory::createOne([
        'code' => 'TEST_CHANNEL_A',
        'name' => 'Test Channel A',
        'hostname' => 'test-a.example.com',
    ]);

    $channel2 = ChannelFactory::createOne([
        'code' => 'TEST_CHANNEL_B',
        'name' => 'Test Channel B',
        'hostname' => 'test-b.example.com',
    ]);

    ChannelOptionFactory::createOne([
        'channel' => $channel1,
        'name' => 'HOMEPAGE_ACCORD_CADRE_CHANNEL_PROPERTY_VALUE_ID',
        'value' => 'value1',
    ]);

    ChannelOptionFactory::createOne([
        'channel' => $channel1,
        'name' => 'HOMEPAGE_PRODUCTS_SELECTION_CHANNEL_PROPERTY_VALUE_ID',
        'value' => 'value2',
    ]);

    ChannelOptionFactory::createOne([
        'channel' => $channel2,
        'name' => 'HOMEPAGE_ACCORD_CADRE_CHANNEL_PROPERTY_VALUE_ID',
        'value' => 'value3',
    ]);

    // Run command
    $command = $this->application->find('channel:clean-obsolete-options');
    $commandTester = new CommandTester($command);
    $commandTester->execute([
        'option-names' => [
            'HOMEPAGE_ACCORD_CADRE_CHANNEL_PROPERTY_VALUE_ID',
            'HOMEPAGE_PRODUCTS_SELECTION_CHANNEL_PROPERTY_VALUE_ID',
        ],
    ]);

    // Check output
    $output = $commandTester->getDisplay();
    \expect($output)->toContain('option(s) obsolète(s) supprimée(s) avec succès');

    // Verify all obsolete options were deleted
    $remainingObsoleteOptions = ChannelOptionFactory::repository()->findBy([
        'name' => [
            'HOMEPAGE_ACCORD_CADRE_CHANNEL_PROPERTY_VALUE_ID',
            'HOMEPAGE_PRODUCTS_SELECTION_CHANNEL_PROPERTY_VALUE_ID',
        ],
    ]);

    \expect($remainingObsoleteOptions)->toBeEmpty();
});

\it('does not remove non-obsolete options', function () {
    // Create test channel and non-obsolete option
    $channel = ChannelFactory::createOne([
        'code' => 'TEST_CHANNEL_SAFE',
        'name' => 'Test Channel Safe',
        'hostname' => 'test-safe.example.com',
    ]);

    ChannelOptionFactory::createOne([
        'channel' => $channel,
        'name' => 'BANNER_HOMEPAGE',
        'value' => 'some_value',
    ]);

    ChannelOptionFactory::createOne([
        'channel' => $channel,
        'name' => 'HOMEPAGE_ACCORD_CADRE_CHANNEL_PROPERTY_VALUE_ID',
        'value' => 'obsolete_value',
    ]);

    // Count options before command
    $countBefore = ChannelOptionFactory::repository()->count(['name' => 'BANNER_HOMEPAGE']);

    // Run command with only the obsolete option
    $command = $this->application->find('channel:clean-obsolete-options');
    $commandTester = new CommandTester($command);
    $commandTester->execute([
        'option-names' => ['HOMEPAGE_ACCORD_CADRE_CHANNEL_PROPERTY_VALUE_ID'],
    ]);

    // Verify option still exists
    $countAfter = ChannelOptionFactory::repository()->count(['name' => 'BANNER_HOMEPAGE']);

    \expect($countAfter)->toBe($countBefore, 'BANNER_HOMEPAGE should not be deleted');
});

\it('shows success message when no obsolete options exist', function () {
    // Run command with no obsolete options (no setup needed)
    $command = $this->application->find('channel:clean-obsolete-options');
    $commandTester = new CommandTester($command);
    $commandTester->execute([
        'option-names' => ['NON_EXISTENT_OPTION'],
    ]);

    // Check output
    $output = $commandTester->getDisplay();
    \expect($output)->toContain('Aucune option obsolète à supprimer');
});

\it('displays channel codes for deleted options', function () {
    // Create test channel and options
    $channel = ChannelFactory::createOne([
        'code' => 'TEST_QANTIS_ACHAT',
        'name' => 'Test Qantis Achat',
        'hostname' => 'test-qantis-achat.example.com',
    ]);

    ChannelOptionFactory::createOne([
        'channel' => $channel,
        'name' => 'HOMEPAGE_ACCORD_CADRE_CHANNEL_PROPERTY_VALUE_ID',
        'value' => 'test_value',
    ]);

    ChannelOptionFactory::createOne([
        'channel' => $channel,
        'name' => 'HOMEPAGE_PRODUCTS_SELECTION_CHANNEL_PROPERTY_VALUE_ID',
        'value' => 'test_value_2',
    ]);

    // Run command
    $command = $this->application->find('channel:clean-obsolete-options');
    $commandTester = new CommandTester($command);
    $commandTester->execute([
        'option-names' => [
            'HOMEPAGE_ACCORD_CADRE_CHANNEL_PROPERTY_VALUE_ID',
            'HOMEPAGE_PRODUCTS_SELECTION_CHANNEL_PROPERTY_VALUE_ID',
        ],
    ]);

    // Check output contains channel code
    $output = $commandTester->getDisplay();
    \expect($output)->toContain('TEST_QANTIS_ACHAT');
});
