<?php

declare(strict_types=1);

namespace App\Tests\Constraints;

use Coduo\PHPMatcher\PHPMatcher;
use PHPUnit\Framework\Constraint\Constraint;
use Symfony\Component\HttpFoundation\Response;

final class ResponseEqualsJson extends Constraint
{
    private PHPMatcher $matcher;
    private string|false $pattern;

    public function __construct(private string $jsonFilePath)
    {
        $this->parseJsonFile();

        $this->matcher = new PHPMatcher();
    }

    public function toString(): string
    {
        return \sprintf('matches pattern %s. Error: %s', $this->jsonFilePath, $this->matcher->error());
    }

    /**
     * @param Response $response
     * @noinspection PhpParameterNameChangedDuringInheritanceInspection
     *
     * {@inheritdoc}
     */
    protected function matches($response): bool
    {
        return $this->matcher->match($response->getContent(), $this->pattern);
    }

    /**
     * @param Response $response
     * @noinspection PhpParameterNameChangedDuringInheritanceInspection
     *
     * {@inheritdoc}
     */
    protected function failureDescription($response): string
    {
        return \sprintf('the Response %s', $this->toString());
    }

    /**
     * @param Response $response
     * @noinspection PhpParameterNameChangedDuringInheritanceInspection
     *
     * {@inheritdoc}
     */
    protected function additionalFailureDescription($response): string
    {
        return (string) $response;
    }

    private function parseJsonFile(): void
    {
        $jsonFileContent = \file_get_contents(\sprintf('%s/../Feature/data/%s', __DIR__, $this->jsonFilePath));

        $this->pattern = \json_encode(\json_decode($jsonFileContent));
    }
}
