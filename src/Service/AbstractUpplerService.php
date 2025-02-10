<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Account;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

abstract class AbstractUpplerService
{
    protected const string SUCCESS = 'success';
    protected const string  ERROR = 'error';

    public function __construct(
        protected UpplerHttpClientService $upplerClient,
        protected LoggerInterface $upplerApiLogger,
    ) {
    }

    public function request(
        string $method,
        string $path,
        array $options = [],
        bool $isAdmin = false,
        bool $withoutToken = false,
        bool $withCache = false,
        bool $addLog = false,
    ): bool|ResponseInterface {
        $inputData = ['method' => $method, 'path' => $path, 'options' => $options];
        try {
            $response = $this->upplerClient->request($method, $path, $options, $isAdmin, $withoutToken, $withCache);

            if ($addLog) {
                $outputData = [
                    'response' => ($response && $response->getContent() !== '') ? $response->toArray(false) : null,
                ];

                $this->logRequest($inputData, $outputData);
            }

            return $response;
        } catch (\Exception $e) {
            $errorCode = $e->getCode();
            $errorMessage = $e->getMessage();
            if ($addLog) {
                $outputData['errorMessage'] = $errorMessage;
                $outputData['errorCode'] = $errorCode;
                $this->logRequest($inputData, $outputData);
            }
            throw new \Exception($errorMessage);
        }
    }

    public function setUserToken(Account $account): bool
    {
        return $this->upplerClient->setUserToken($account);
    }

    protected function getSession(): SessionInterface
    {
        return $this->upplerClient->requestStack->getSession();
    }

    protected function getAccount(): ?Account
    {
        return $this->getSession()->get('account') ?? null;
    }

    protected function logRequest(array $inputData, array $outputData): void
    {
        $type = $this->isError($outputData) ? self::ERROR : self::SUCCESS;
        $url = $inputData['path'];
        $method = $inputData['method'];
        $options = $inputData['options'];
        try {
            match ($type) {
                self::SUCCESS => $this->upplerApiLogger->info($this->buildSuccessMessage($url, $method, $options)),
                self::ERROR => $this->upplerApiLogger->error($this->buildErrorMessage($url, $method, $options, $outputData)),
            };
        } catch (\Exception $e) {
            return;
        }
    }

    private function buildErrorMessage(string $path, string $method, array $options, array $outputData): string
    {
        return 'REQUEST FAILED WITH method '.$method.' - path:'.$path.' - options: '.\json_encode($options).' - WITH ERRORS CONTENT '.\json_encode($outputData).\PHP_EOL;
    }

    private function buildSuccessMessage(string $path, string $method, array $options): string
    {
        return 'REQUEST SUCCESS WITH method '.$method.' - path:'.$path.' - options: '.\json_encode($options).\PHP_EOL;
    }

    private function isError(array $outputData): bool
    {
        return !empty($outputData['errorCode']) || (!empty($outputData['code']) && $outputData['code'] >= 400 && $outputData['code'] <= 600);
    }
}
