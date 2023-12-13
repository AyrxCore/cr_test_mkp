<?php

declare(strict_types=1);

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Dto\AccountAccordCadre;
use App\Entity\AccordStatut;
use App\Entity\Adherent;
use App\Repository\AdherentRepository;
use App\Repository\ChannelRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Uid\Uuid;

class AdherentPersister implements ContextAwareDataPersisterInterface
{
    public function __construct(
        private AdherentRepository $adherentRepository,
        private ChannelRepository $channelRepository,
        private EntityManagerInterface $em,
        private NormalizerInterface $normalizer,
        private UserPasswordHasherInterface $userPasswordHasher,
    ) {
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof Adherent;
    }

    /**
     * @param Adherent $data
     */
    public function persist($data, array $context = [])
    {
        $adh = $this->adherentRepository->find($data->getId());
        if ($adh === null) {
            return null;
        }

        $channel = $this->channelRepository->findOneByCode($data->getChannelCode());

        if (!$channel) {
            throw new BadRequestHttpException('Channel not found');
        }

        $adh->setChannel($channel);
        $adh->setReducceCode($data->getReducceCode());
        $adh->setsiret($data->getsiret());
        $adh->setStreet($data->getStreet());
        $adh->setCity($data->getCity());
        $adh->setPostalcode($data->getPostalcode());
        $adh->setCountry($data->getCountry());
        $adh->setHashkey($data->getHashkey());
        foreach ($data->getAttachments() as $attachment) {
            $accordStatut = $this->em->getRepository(AccordStatut::class)->findOneBy([
                'adherent' => $data->getId(),
                'accordId' => $attachment['accordId'],
            ]);
            if ($accordStatut) {
                // ne pas ecraser Pending par NotActivated
                if (!($accordStatut->getStatus() === AccountAccordCadre::PROCESS_STATUS_PENDING
                    && $attachment['status'] === AccountAccordCadre::PROCESS_STATUS_NOT_ACTIVATED)
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
