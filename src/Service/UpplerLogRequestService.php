<?php

declare(strict_types=1);

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class UpplerLogRequestService
{
    protected const string SUCCESS = 'success';
    protected const string  ERROR = 'error';

    public function __construct(protected readonly LoggerInterface $upplerApiLogger)
    {
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function logRequest(array $inputData, ?ResponseInterface $response, array $errors = []): void
    {
        $outputData = [
            'response' => \json_decode($response->getContent(false), true) ?? null,
            'status' => $response->getStatusCode(),
            'errors' => $errors,
        ];

        $type = $this->isError($outputData) ? self::ERROR : self::SUCCESS;
        $url = $inputData['path'];
        $method = $inputData['method'];
        $options = $inputData['options'];

        try {
            match ($type) {
                self::SUCCESS => $this->upplerApiLogger->info($this->buildSuccessMessage($url, $method, $options, $outputData)),
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

    private function buildSuccessMessage(string $path, string $method, array $options, array $outputData): string
    {
        return 'REQUEST SUCCESS WITH method '.$method.' - path:'.$path.' - options: '.\json_encode($options).' - WITH CONTENT '.\json_encode($outputData).\PHP_EOL;
    }

    private function isError(array $outputData): bool
    {
        return !empty($outputData['errors']['code'])
            || (
                !empty($outputData['response']['error']['code'])
                && $outputData['response']['error']['code'] >= 400
                && $outputData['response']['error']['code'] <= 600
            );
    }
}
