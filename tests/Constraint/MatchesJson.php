<?php

declare(strict_types=1);

namespace App\Tests\Constraint;

use App\Tests\Feature\Helper\JsonHelper;
use Coduo\PHPMatcher\PHPMatcher;
use PHPUnit\Framework\Constraint\Constraint;
use Symfony\Component\HttpFoundation\Response;

final class MatchesJson extends Constraint
{
    private PHPMatcher $matcher;
    private string|false $pattern;

    public function __construct(private string $jsonFilePath)
    {
        $this->matcher = new PHPMatcher();
        $this->pattern = JsonHelper::parseJsonDataFile($this->jsonFilePath);
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
}
