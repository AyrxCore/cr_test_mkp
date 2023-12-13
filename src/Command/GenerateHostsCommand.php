<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

#[AsCommand(
    name: 'generate:hosts',
    description: "Generate the list of channels' hosts to be added to the /etc/hosts file",
)]
class GenerateHostsCommand extends Command
{
    private const HOST_PATTERN = '/^(?:https?:\/\/)?([a-z\-.]+)\.[a-z]+$/';

    public function __construct(private ContainerInterface $container)
    {
        parent::__construct("Generate the list of channels' hosts to be added to the /etc/hosts file");
    }

    protected function configure(): void
    {
        $this->addOption('url', 'u', description: 'Show URLs instead of IPs and hostnames');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (\getenv('APP_ENV') !== 'dev') {
            throw new \RuntimeException('This command should only be run in "dev" environment');
        }

        $channels = $this->container->getParameter('channels');

        if (!$input->getOption('url')) {
            $output->writeln('<info>Copy/paste the following lines in your /etc/hosts file:</info>');
            $output->writeln('# Qantis');
        }

        foreach ($channels as $channel) {
            $output->writeln($this->getOutputValue($channel, $input->getOption('url')));
        }

        return Command::SUCCESS;
    }

    private function getOutputValue(array $channel, bool $full = false): string
    {
        if ($full) {
            return \sprintf('http://%s', \preg_replace(
                self::HOST_PATTERN,
                '$1.local:8087',
                $channel['hostname']
            ));
        }

        return \sprintf('127.0.0.1       %s', \preg_replace(
            self::HOST_PATTERN,
            '$1.local',
            $channel['hostname']
        ));
    }
}
