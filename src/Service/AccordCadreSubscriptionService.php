<?php

declare(strict_types=1);

namespace App\Service;

use App\Context\ChannelContext;
use App\Dto\AccountAccordCadre;
use App\Entity\AccordStatut;
use App\Entity\Account;
use App\Entity\LogAccordStatutRequest;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Uid\Uuid;
use Twig\Environment;

class AccordCadreSubscriptionService
{
    public function __construct(
        private EntityManagerInterface $em,
        private MailerProvider $mailerProvider,
        private LoggerInterface $logger,
        private Environment $twig,
        private string $emailFrom,
        private string $sugarLink,
        private array $stellantisMailing
    ) {
    }

    /**
     * @throws \Exception
     */
    public function subscription(array $params, string $accountId, ChannelContext $channelContext): string
    {
        $account = $this->em->getRepository(Account::class)->find($accountId);

        if (!$account) {
            throw new \Exception('Account not found');
        }

        try {
            foreach ($this->getDataToSendEmail($params, $account, $channelContext) as $data) {
                $this->mailerProvider->send(
                    $data['from'],
                    $data['to'],
                    $data['subject'],
                    $this->twig->render($data['template'], $data['params']),
                    $data['options'] ?? []
                );
            }

            return $this->processAccordStatus($account, $params);
        } catch (\Exception $exception) {
            $this->logger->critical(
                "Erreur d'envoi de demande de subscription "
                .$account->getUser()->getemail().' '.$account->getAdherent()->getName().' : '.
                $exception->getMessage()
            );

            return AccountAccordCadre::PROCESS_STATUS_NOT_ACTIVATED;
        }
    }

    private function processAccordStatus(Account $account, array $params): string
    {
        try {
            $accordStatut = $this->em->getRepository(AccordStatut::class)->findOneBy([
                'adherent' => $account->getAdherent()->getId(),
                'accordId' => $params['accordId'],
            ]);

            if (!$accordStatut) {
                $accordStatut = new AccordStatut();
                $accordStatut->setAdherent($account->getAdherent());
                $accordStatut->setAccordId(new Uuid($params['accordId']));
                $accordStatut->setAccordStatutRequestAt(new \DateTime('now'));
                $accordStatut->setStatus(AccountAccordCadre::PROCESS_STATUS_PENDING);

                $log = new LogAccordStatutRequest();
                $log->setAccordId(new Uuid($params['accordId']));
                $log->setAccount($account);
                $log->setCreatedAt(new \DateTimeImmutable('now'));
                $this->em->persist($log);
            } else {
                if ($accordStatut->getStatus() === AccountAccordCadre::PROCESS_STATUS_NOT_ACTIVATED) {
                    $accordStatut->setStatus(AccountAccordCadre::PROCESS_STATUS_PENDING);
                }
            }

            $this->em->persist($accordStatut);
            $this->em->flush();

            return $accordStatut->getStatus();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    private function getDataToSendEmail(array $params, Account $account, ChannelContext $channelContext): array
    {
        $emailParams[] = [
            'from' => $this->emailFrom,
            'to' => $channelContext->getChannel()?->getChannelParameter()?->getEmail(),
            'subject' => 'MARKETPLACE - Bénéficier des conditions pour la FAT '.$params['accordName'],
            'template' => 'mails/request.accord.subscription.html.twig',
            'params' => [
                'fat' => $params['accordName'],
                'email' => $account->getUser()->getemail(),
                'nom' => $account->getUser()->getFirstName().' '.$account->getUser()->getLastName(),
                'societe' => $account->getAdherent()->getName(),
                'sugarLink' => $this->sugarLink.$account->getAdherent()->getId(),
            ],
        ];

        $parameters = $this->stellantisMailing;
        if (\in_array($params['accordId'], $parameters['ACCORDS_IDS'], true)) {
            // send adherent service mail
            $emailParams[] = [
                'from' => $parameters['ADHERENT_MAIL']['FROM'],
                'to' => \explode(';', $parameters['ADHERENT_MAIL']['TO']),
                'subject' => 'Marketplace - '.$account->getAdherent()->getSiret().' - Demande de rattachement au contrat QANTIS/STELLANTIS',
                'template' => 'mails/stellantis/to_adherent_service.html.twig',
                'params' => [
                    'account' => $account,
                    'horodatage' => new \DateTime('now'),
                ],
            ];

            // send Stellantis mail
            $emailParams[] = [
                'from' => $parameters['STELLANTIS_MAIL']['FROM'],
                'to' => \explode(';', $parameters['STELLANTIS_MAIL']['TO']),
                'subject' => $account->getAdherent()->getSiret().' - Demande de rattachement au contrat STELLANTIS',
                'template' => 'mails/stellantis/to_stellantis.html.twig',
                'params' => [
                    'account' => $account,
                    'horodatage' => new \DateTime('now'),
                ],
                'options' => ['cc' => \explode(',', $parameters['STELLANTIS_MAIL']['CC'])],
            ];
        }

        return $emailParams;
    }
}
