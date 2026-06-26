<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Filter;

use App\Filter\DtoFilterInterface;
use App\Filter\FilterableInterface;
use App\Service\Filter\DtoFilterService;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class DtoFilterServiceTest extends TestCase
{
    private DtoFilterService $filterService;
    private LoggerInterface $logger;

    protected function setUp(): void
    {
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->filterService = new DtoFilterService([], $this->logger);
    }

    public function testFilterWithNoFiltersReturnsAllObjects(): void
    {
        $objects = [
            $this->createMockFilterable(['tags' => ['premium']]),
            $this->createMockFilterable(['tags' => ['basic']]),
        ];

        $result = $this->filterService->filter($objects);

        $this->assertCount(2, $result);
    }

    public function testFilterWithAllowingFilterReturnsMatchingObjects(): void
    {
        $filter = $this->createMock(DtoFilterInterface::class);
        $filter->method('shouldInclude')
            ->willReturnCallback(function (FilterableInterface $object) {
                $criteria = $object->getFilterCriteria();
                return !isset($criteria['tags']) || \in_array('premium', $criteria['tags']);
            });
        $filter->method('getName')->willReturn('test_filter');

        $this->filterService = new DtoFilterService([$filter], $this->logger);

        $objects = [
            $this->createMockFilterable(['tags' => ['premium']]), // Should pass
            $this->createMockFilterable(['tags' => ['basic']]),   // Should fail
            $this->createMockFilterable([]),                      // Should pass (no tags)
        ];

        $result = $this->filterService->filter($objects);

        $this->assertCount(2, $result);
    }

    public function testShouldIncludeWithNoFiltersReturnsTrue(): void
    {
        $object = $this->createMockFilterable(['tags' => ['premium']]);

        $result = $this->filterService->shouldInclude($object);

        $this->assertTrue($result);
    }

    public function testShouldIncludeWithDenyingFilterReturnsFalse(): void
    {
        $filter = $this->createMock(DtoFilterInterface::class);
        $filter->method('shouldInclude')->willReturn(false);
        $filter->method('getName')->willReturn('denying_filter');

        $this->filterService = new DtoFilterService([$filter], $this->logger);

        $object = $this->createMockFilterable(['tags' => ['premium']]);

        $result = $this->filterService->shouldInclude($object);

        $this->assertFalse($result);
    }

    private function createMockFilterable(array $criteria): FilterableInterface
    {
        $mock = $this->createMock(FilterableInterface::class);
        $mock->method('getFilterCriteria')->willReturn($criteria);

        return $mock;
    }
}
