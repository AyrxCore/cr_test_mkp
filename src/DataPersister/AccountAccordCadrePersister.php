<?php

declare(strict_types=1);

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\AccountAccordCadre;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Service\Attribute\Required;

class AccountAccordCadrePersister implements ContextAwareDataPersisterInterface
{
    #[Required]
    public EntityManagerInterface $em;

    public function supports($data, array $context = []): bool
    {
        return $data instanceof AccountAccordCadre;
    }

    /**
     * @param AccountAccordCadre $data
     */
    public function persist($data, array $context = [])
    {
        $accountAccordCadre = $this->em->getRepository(AccountAccordCadre::class)->find($data->getId());

        $accountAccordCadre->setAccountId($data->getAccountId());
        $accountAccordCadre->setAccordCadreId($data->getAccordCadreId());
        $accountAccordCadre->setStatus($data->getStatus());

        $this->em->persist($accountAccordCadre);

        $this->em->flush();

        return $accountAccordCadre;
    }

    public function remove($data, array $context = [])
    {
        // TODO: Implement remove() method.
    }
}
