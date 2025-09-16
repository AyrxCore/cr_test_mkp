<?php

declare(strict_types=1);

namespace App\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use App\Repository\AccountRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class FavoritePersistProcessor implements ProcessorInterface
{
    public function __construct(public EntityManagerInterface $em, private readonly AccountRepository $accountRepository, public RequestStack $requestStack)
    {
    }

    /**
     * @throws \Exception
     */
    public function process($data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        try {
            if ($operation instanceof Post) {
                $session = $this->requestStack->getSession();
                $accountId = $session?->get('account')?->getId();
                if ($accountId) {
                    $account = $this->accountRepository->find($accountId);
                    if ($account !== null) {
                        $data->setAccount($account);
                    } else {
                        throw new \RuntimeException('Compte introuvable pour l\'identifiant présent en session');
                    }
                } else {
                    throw new \RuntimeException('Aucun compte en session');
                }
            }
            $this->em->persist($data);
            $this->em->flush();

            return $data;
        } catch (UniqueConstraintViolationException $uniqueConstraintViolationException) {
            throw new ConflictHttpException($uniqueConstraintViolationException->getMessage());
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }
}
