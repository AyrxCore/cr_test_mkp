<?php

namespace App\Command;

use App\Service\UserService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\Service\Attribute\Required;

#[AsCommand(
    name: 'user:demote',
    description: 'Remove role to user',
)]
class DemoteUserCommand extends Command
{
    #[Required]
    public UserService $userService;

    public function __construct()
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDefinition(array(
                new InputArgument('username', InputArgument::REQUIRED, 'Email'),
                new InputArgument('role', InputArgument::OPTIONAL, 'Le rôle')
            ));;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('username');
        $role = $input->getArgument('role');

        if (null === $role) {
            throw new \RuntimeException('Vous devez spécifier le role..');
        }

        $this->userService->demoteUser($username, $role);

        $output->writeln(sprintf('Le rôle "%s" a été révoqué pour l\'utilisateur "%s". Cette modification ne sera effective que lors de la prochaine session de l\'utilisateur', $role, $username));

        return Command::SUCCESS;
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $questions = array();

        if (!$input->getArgument('username')) {
            $question = new Question('Veuillez saisir l\'identifiant de l\'utilisateur à modifier : ');
            $question->setValidator(function ($username) {
                if (empty($username)) {
                    throw new \Exception('L\'identifiant ne peut être vide');
                }

                return trim($username);
            });
            $questions['username'] = $question;
        }

        if (!$input->getArgument('role')) {
            $question = new Question('Veuillez saisir le rôle à révoquer : ');
            $question->setValidator(function ($role) {
                if (empty($role)) {
                    throw new \Exception('Le rôle ne peut être vide');
                }

                return trim($role);
            });
            $questions['role'] = $question;
        }

        foreach ($questions as $name => $question) {
            $answer = $this->getHelper('question')->ask($input, $output, $question);
            $input->setArgument($name, $answer);

        }
    }
}
