<?php

declare(strict_types=1);

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\SavedCart;
use App\Service\SavedCartService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Contracts\Service\Attribute\Required;

class SavedCartPersister implements ContextAwareDataPersisterInterface
{
    #[Required]
    public EntityManagerInterface $em;

    #[Required]
    public NormalizerInterface $normalizer;

    #[Required]
    public Security $security;

    #[Required]
    public RequestStack $requestStack;

    #[Required]
    public SavedCartService $savedCartService;

    public function supports($data, array $context = []): bool
    {
        return $data instanceof SavedCart;
    }

    /**
     * @param SavedCart $data
     * @throws Exception
     */
    public function persist($data, array $context = [])
    {
        try {
            if (isset($context["collection_operation_name"]) && ('create' === $context["collection_operation_name"])) {
                $savedCart = $this->savedCartService->create($data);
            } else  {
                $savedCart = $this->savedCartService->update($data);
            }
            return $savedCart;
        } catch (Exception $exception) {
            throw  new Exception($exception->getMessage());
        }
    }

    /**
     * @param SavedCart $data
     * @throws Exception
     */
    public function remove($data, array $context = []): bool
    {
        return $this->savedCartService->removeSavedCart($data);
    }
}
