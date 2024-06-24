<?php

declare(strict_types=1);

namespace App\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\SecurityBundle\Security;

readonly class UserMeItemProvider implements ProviderInterface
{
    public function __construct(private UserRepository $userRepository, private Security $security)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        /** @var User $currentUser */
        $currentUser = $this->security->getUser();

        return $this->userRepository->find($currentUser->getId());
    }
}
