<?php

declare(strict_types=1);

namespace App\Command\Djust;

use App\Service\Djust\DjustHttpClientService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

#[AsCommand(
    name: 'app:test-djust-api',
    description: 'Test DJUST API GET request using DjustHttpClientService',
)]
class TestDjustHttpClientServiceCommand extends Command
{
    public function __construct(
        private readonly DjustHttpClientService $djustHttpClient,
        private readonly RequestStack $requestStack,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('endpoint', InputArgument::REQUIRED, 'L\'endpoint à tester (ex: /users, /contracts)')
            ->addOption('params', 'p', InputOption::VALUE_OPTIONAL, 'Paramètres d\'URL au format JSON pour remplacer les placeholders (ex: {"id":"123","userId":"456"})')
            ->addOption('query', null, InputOption::VALUE_OPTIONAL, 'Paramètres de requête au format JSON (ex: {"limit":10,"page":1})')
            ->addOption('headers', null, InputOption::VALUE_OPTIONAL, 'Headers supplémentaires au format JSON (ex: {"X-Custom":"value"})')
            ->addOption('method', 'm', InputOption::VALUE_OPTIONAL, 'Méthode HTTP à utiliser', 'GET')
            ->addOption('djClient', 'djC', InputOption::VALUE_OPTIONAL, 'Type de client à utiliser', 'ACCOUNT')
            ->addOption('data', 'd', InputOption::VALUE_OPTIONAL, 'Données à envoyer au format JSON (pour POST, PUT, PATCH)')
            ->setHelp(
                <<<'EOT'
Cette commande permet de tester l'API DJUST en utilisant le service DjustHttpClientService.

Exemples d'utilisation :

  # Requête GET simple
  <info>php bin/console app:test-djust-api /v1/jobs</info>

  # Requête GET avec paramètres de requête
  <info>php bin/console app:test-djust-api /v1/jobs --query='{"size":1,"page":1}'</info>

  # Requête POST avec données
  <info>php bin/console app:test-djust-api /v1/customer-users --method=POST --data='{"accountIds":["0000000302"],"civility":"MR","externalId": "6320721e-c498-11ed-94e2-021d5312eaee","firstName":"John","lastName":"Doe","groups":["FOC_Admin"],"password":"pswd","email":"test@mail.com"}'</info>

  # Requête POST avec données
  <info>php bin/console app:test-djust-api /v1/product-tags --method=POST --data='{"name":"test_tag"}'</info>

 # Requête DELETE
  <info>php bin/console app:test-djust-api /v1/product-tags/:id --method=DELETE --params='{"id":"0000000368"}'</info>

  # Requête PUT
  <info>php bin/console app:test-djust-api /v1/product-tags/:id --method=PUT --params='{"id":"0000000368"}' --data='{"name":"nouveau_tag"}'</info>

  # Requête PATCH
  <info>php bin/console app:test-djust-api /v1/customer-users --method=PATCH --data='[{"id":"0000000364","status":"INACTIVE"}]'</info>


EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->createConsoleSession();

        $endpoint = $input->getArgument('endpoint');
        $method = \strtoupper($input->getOption('method') ?? 'GET');
        $djClient = \strtoupper($input->getOption('djClient') ?? 'ACCOUNT');

        // Validation de l'endpoint
        if (!\str_starts_with($endpoint, '/')) {
            $io->error('L\'endpoint doit commencer par "/" (ex: /users)');

            return Command::FAILURE;
        }

        $io->title('Test DJUST API');
        $io->section('Configuration de la requête');

        try {
            // Parse des paramètres optionnels
            $urlParams = $this->parseJsonOption($input->getOption('params'), 'params');
            $queryParams = $this->parseJsonOption($input->getOption('query'), 'query');
            $headers = $this->parseJsonOption($input->getOption('headers'), 'headers');
            $data = $this->parseJsonOption($input->getOption('data'), 'data');

            // Remplacement des paramètres dans l'URL
            $finalEndpoint = $this->replaceUrlParameters($endpoint, $urlParams);
            $isOperator = $djClient === 'OPERATOR';

            $io->definitionList(
                ['URL de base : https://djust-api.pre-prod.djust-app.com/qantis'],
                ['Endpoint original : '.$endpoint],
                ['Endpoint final : '.$finalEndpoint],
                ['Méthode HTTP : '.$method],
                ['Type de client : '.$djClient],
            );

            if (!empty($urlParams)) {
                $io->writeln('<info>Paramètres d\'URL:</info> '.\json_encode($urlParams, \JSON_PRETTY_PRINT));
            }

            if (!empty($queryParams)) {
                $io->writeln('<info>Paramètres de requête:</info> '.\json_encode($queryParams, \JSON_PRETTY_PRINT));
            }

            if (!empty($headers)) {
                $io->writeln('<info>Headers personnalisés:</info> '.\json_encode($headers, \JSON_PRETTY_PRINT));
            }

            if (!empty($data)) {
                $io->writeln('<info>Données à envoyer:</info> '.\json_encode($data, \JSON_PRETTY_PRINT));
            }

            $io->newLine();
            $io->writeln('🚀 <comment>Envoi de la requête...</comment>');

            // Exécution de la requête selon la méthode
            $startTime = \microtime(true);

            $response = match ($method) {
                'GET' => $this->djustHttpClient->get($finalEndpoint, $queryParams, $headers, $isOperator),
                'POST' => $this->djustHttpClient->post($finalEndpoint, $data, $headers, $isOperator),
                'PUT' => $this->djustHttpClient->put($finalEndpoint, $data, $headers, $isOperator),
                'PATCH' => $this->djustHttpClient->patch($finalEndpoint, $data, $headers, $isOperator),
                'DELETE' => $this->djustHttpClient->delete($finalEndpoint, $headers, $isOperator),
                default => throw new \InvalidArgumentException("Méthode HTTP non supportée: $method"),
            };

            $executionTime = \round((\microtime(true) - $startTime) * 1000, 2);

            $io->success('✅ Requête exécutée avec succès!');

            $io->section('Résultats');
            $io->definitionList(
                ['Temps d\'exécution : '.$executionTime.' ms'],
                ['Taille de la réponse : '.\count($response).' éléments']
            );

            // Affichage de la réponse
            if (empty($response)) {
                $io->note('Réponse vide');
            } else {
                $io->writeln('<info>Réponse de l\'API:</info>');
                $io->writeln('<fg=cyan>'.\json_encode($response, \JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE).'</>');
            }

            return Command::SUCCESS;
        } catch (\InvalidArgumentException $e) {
            $io->error('Erreur de paramètre: '.$e->getMessage());

            return Command::FAILURE;
        } catch (\Exception $e) {
            $io->error([
                '❌ Erreur lors de l\'appel à l\'API DJUST:',
                'Message: '.$e->getMessage(),
                'Type: '.$e::class,
            ]);

            if ($output->isVerbose()) {
                $io->section('Stack trace');
                $io->writeln('<fg=red>'.$e->getTraceAsString().'</>');
            }

            return Command::FAILURE;
        }
    }

    private function createConsoleSession(): void
    {
        // Créer une session avec un storage en mémoire pour la console
        $session = new Session(new MockArraySessionStorage());

        // Créer une requête factice
        $request = new Request();
        $request->setSession($session);

        // Ajouter la requête au RequestStack
        $this->requestStack->push($request);
    }

    /**
     * Parse une option JSON et retourne un tableau.
     */
    private function parseJsonOption(?string $jsonString, string $optionName): array
    {
        if (empty($jsonString)) {
            return [];
        }

        $decoded = \json_decode($jsonString, true);

        if (\json_last_error() !== \JSON_ERROR_NONE) {
            throw new \InvalidArgumentException(
                "Erreur de parsing JSON pour l'option --{$optionName}: ".\json_last_error_msg()
            );
        }

        if (!\is_array($decoded)) {
            throw new \InvalidArgumentException(
                "L'option --{$optionName} doit être un objet JSON valide"
            );
        }

        return $decoded;
    }

    private function replaceUrlParameters(string $endpoint, array $params): string
    {
        $finalEndpoint = $endpoint;

        foreach ($params as $key => $value) {
            $placeholder = ':'.$key;
            if (\str_contains($finalEndpoint, $placeholder)) {
                $finalEndpoint = \str_replace($placeholder, (string) $value, $finalEndpoint);
            }
        }

        // Vérifier s'il reste des placeholders non remplacés
        if (\preg_match('/:(\w+)/', $finalEndpoint, $matches)) {
            throw new \InvalidArgumentException(
                "Paramètre manquant pour le placeholder ':{$matches[1]}' dans l'endpoint"
            );
        }

        return $finalEndpoint;
    }
}
