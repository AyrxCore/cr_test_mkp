<?php

declare(strict_types=1);

namespace App\Command\Channel;

use App\Entity\Channel;
use App\Entity\ChannelOption;
use App\Service\Channel\ChannelOptionSynchronizer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'channel:update-option',
    description: 'Update option to channel',
)]
class ChannelOptionUpdateCommand extends Command
{
    public function __construct(private EntityManagerInterface $entityManager, private ChannelOptionSynchronizer $channelOptionsRetriever, string $name = null)
    {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->addArgument('code', InputArgument::REQUIRED, 'Channel code');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        try {
            $questionHelper = $this->getHelper('question');
            $code = $input->getArgument('code');

            $channel = $this->entityManager->getRepository(Channel::class)->findOneBy(['code' => $code]);
            if (!$channel) {
                throw new \Exception('Channel not found');
            }

            $optionNames = \array_map(function ($option) {
                return $option->getName().' => '.$option->getValue() ?? ')';
            }, $channel->getChannelOptions()->toArray());

            $choiceQuestion = new ChoiceQuestion(
                'Veuillez sélectionner une option (par défaut, la première option est sélectionnée):',
                $optionNames,
                0
            );

            $choiceQuestion->setErrorMessage('Option %s est invalide.');
            $optionName = $questionHelper->ask($input, $output, $choiceQuestion);

            $valueQuestion = new Question('Veuillez entrer la valeur pour cette option: ');
            $value = $questionHelper->ask($input, $output, $valueQuestion);

            $selectedOptionName = \explode(' => ', $optionName)[0];
            $selectedOption = $this->entityManager->getRepository(ChannelOption::class)->findOneBy(['name' => $selectedOptionName, 'channel' => $channel]);
            $selectedOption->setValue($value);

            $this->entityManager->persist($selectedOption);
            $this->entityManager->flush();

            $output->writeln('Option mise à jour avec succès!');

            foreach ($channel->getChannelOptions() as $option) {
                $output->writeln($option->getName().': '.$option->getValue());
            }

            $io->success(\sprintf('Your option %s with value %s was successfully added to channel %s', $selectedOptionName, $value, $code));

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }
    }
}
