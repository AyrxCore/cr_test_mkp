<?php

declare(strict_types=1);

namespace App\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Doctrine\ORM\EntityManagerInterface;

readonly class SavedCartRemoveProcessor implements ProcessorInterface
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function process($data, Operation $operation, array $uriVariables = [], array $context = []): true
    {
        $this->em->remove($data);
        $this->em->flush();

        return true;
    }
}
