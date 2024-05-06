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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Uid\Uuid;

class AdherentPersister implements ContextAwareDataPersisterInterface
{
    public function __construct(private AdherentRepository $adherentRepository, private ChannelRepository $channelRepository, private EntityManagerInterface $em)
    {
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof Adherent;
    }

    /**
     * @param Adherent $data
     */
    public function persist($data, array $context = []): ?Adherent
    {
        $adherent = $this->adherentRepository->find($data->getId());

        if (!$adherent) {
            throw new NotFoundHttpException('Adherent not found');
        }

        $channel = $this->channelRepository->findOneByCode($data->getChannelCode());
        if (!$channel) {
            throw new BadRequestHttpException('Channel not found');
        }

        foreach ($data->getAttachments() as $attachment) {
            $accordStatut = $this->em->getRepository(AccordStatut::class)->findOneBy([
                'adherent' => $data->getId(),
                'accordId' => $attachment['accordId'],
            ]);
            if ($accordStatut) {
                // Ne pas écraser Pending par NotActivated
                if (
                    !($accordStatut->getStatus() === AccountAccordCadre::PROCESS_STATUS_PENDING
                    && $attachment['status'] === AccountAccordCadre::PROCESS_STATUS_NOT_ACTIVATED)
                ) {
                    $accordStatut->setStatus($attachment['status']);
                    $this->em->persist($accordStatut);
                }
            } else {
                $accordStatut = new AccordStatut();
                $accordStatut->setAdherent($adherent);
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
