<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Dto\AccountAccordCadre;
use App\Entity\AccordStatut;
use App\Entity\Adherent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\Service\Attribute\Required;

class AdherentPersister implements ContextAwareDataPersisterInterface
{

    #[Required]
    public EntityManagerInterface $em;

    #[Required]
    public UserPasswordHasherInterface $userPasswordHasher;

    #[Required]
    public NormalizerInterface $normalizer;

    public function supports($data, array $context = []): bool
    {
        return $data instanceof Adherent;
    }

    /**
     * @param  Adherent  $data
     */
    public function persist($data, array $context = [])
    {
        $adh = $this->em->getRepository(Adherent::class)->find($data->getId());
        if (null === $adh) {
            return null;
        }

        $adh->setReducceCode($data->getReducceCode());
        foreach ($data->getAttachments() as $key => $attachment) {
            $accordStatut = $this->em->getRepository(AccordStatut::class)->findOneBy([
                'adherent' => $data->getId(),
                'accordId' => $attachment['accordId'],
            ]);
            if ($accordStatut) {
                // ne pas ecraser Pending par NotActivated
                if (!(AccountAccordCadre::PROCESS_STATUS_PENDING === $accordStatut->getStatus()
                    && $attachment === AccountAccordCadre::PROCESS_STATUS_NOT_ACTIVATED)
                ) {
                    $accordStatut->setStatus($attachment['status']);
                    $this->em->persist($accordStatut);
                }
            } else {
                $accordStatut = new AccordStatut();
                $accordStatut->setAdherent($adh);
                $accordStatut->setAccordId(new Uuid($attachment['accordId']));
                $accordStatut->setStatus($attachment['status']);
                $this->em->persist($accordStatut);
            }
        }
        $this->em->flush();

        return $data;
    }

    public function remove($data, array $context = [])
    {
    }

}
