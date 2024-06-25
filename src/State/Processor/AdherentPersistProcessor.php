<?php

declare(strict_types=1);

namespace App\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\AccountAccordCadre;
use App\Entity\AccordStatut;
use App\Repository\AccordStatutRepository;
use App\Repository\AdherentRepository;
use App\Repository\ChannelRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Uid\Uuid;

readonly class AdherentPersistProcessor implements ProcessorInterface
{
    public function __construct(
        private AccordStatutRepository $accordStatutRepository,
        private AdherentRepository $adherentRepository,
        private ChannelRepository $channelRepository,
        private EntityManagerInterface $em,
    ) {
    }

    public function process($data, Operation $operation, array $uriVariables = [], array $context = []): mixed
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
            $accordStatut = $this->accordStatutRepository->findOneBy([
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
}
