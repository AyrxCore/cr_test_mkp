<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\AccordCadre;
use App\Entity\AccountAccordCadre;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\Service\Attribute\Required;

class UpplerAccordCadreService extends AbstractUpplerProductService
{
    public function getAccordsCadresByParams(array $options = [], array $filters = []): array | null
    {
        $res = $this->searchProductsByParams($options, $filters);

        if (null === $res) {
            return null;
        }

        $accordsCadre = [];
        foreach ($res->results as $result) {
            $accordsCadre[] = $this->getAccordCadre($result->id);
        }
        return $accordsCadre;
    }

    /**
     * @throws ExceptionInterface
     */
    public function getAccordCadre(int $productId, ?string $accountId = null)
    {
        $res = $this->getObject($productId);

        if (null === $res) {
            return null;
        }

        return $this->populateAccordCadre($res, $accountId);
    }

    private function populateAccordCadre($remoteAccordCadre, ?string $accountId = null)
    {
        $accordCadre = new AccordCadre();
        $accordCadre->setId($remoteAccordCadre->id);
        $accordCadre->setName($remoteAccordCadre->name->default);
        $accordCadre->setDescription($remoteAccordCadre->description->default ?? null);
        $accordCadre->setReference($remoteAccordCadre->reference);

        $categories = [];
        foreach ($remoteAccordCadre->categories as $category) {
            $categories[$category->id] = $category->name->default;
        }
        $accordCadre->setCategories($categories);

        $properties = [];
        foreach($remoteAccordCadre->properties as $property) {
            $properties[$property->property->name->fr] = $property->value;
        }
        $accordCadre->setProperties($properties);

        if ($accountId) {
            $accountAccordCadre = $this->em->getRepository(AccountAccordCadre::class)->findOneBy(['accordCadreId' => $remoteAccordCadre->id, 'accountId' => $accountId]);
            if (null === $accountAccordCadre) {
                $accountAccordCadre = new AccountAccordCadre();
                $accountAccordCadre->setAccountId($accountId);
                $accountAccordCadre->setStatus(AccordCadre::PROCESS_STATUS_NOT_ACTIVATED);
                $accountAccordCadre->setAccordCadreId($remoteAccordCadre->id);
                $this->em->persist($accountAccordCadre);
                $this->em->flush();
            }

            $accordCadre->setAccountAccordCadre($accountAccordCadre);
        }

        return $accordCadre;
    }
}
