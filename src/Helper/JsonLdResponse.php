<?php

namespace App\Helper;

use Symfony\Component\HttpFoundation\JsonResponse;

class JsonLdResponse
{
    public static function render(string $type, int $statusCode, string $message): JsonResponse
    {
        $exceptionData = [
            '@context' => 'https://schema.org',
            '@type' => $type,
            'statusCode' => $statusCode,
            'message' => $message,
        ];

        $response = new JsonResponse($exceptionData, $statusCode);
        $response->headers->set('Content-Type', 'application/ld+json');

        return $response;
    }
}
