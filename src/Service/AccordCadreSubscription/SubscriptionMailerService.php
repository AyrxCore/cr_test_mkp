<?php

declare(strict_types=1);

namespace App\Service\AccordCadreSubscription;

use App\Entity\Account;
use App\Service\MailerProvider;
use Exception;
use Psr\Log\LoggerInterface;
use Twig\Environment;

class SubscriptionMailerService
{
    public function __construct(
        private MailerProvider $mailerProvider,
        private LoggerInterface $logger,
        private Environment $twig,
        private string $emailFrom,
        private string $sugarLink,
        private array $stellantisParams
    ) {
    }

    /**
     * @throws Exception
     */
    public function sendMail(
        Account $account,
        string $email,
        ?string $accorName = null,
        bool $isStellantis = false
    ): void {
        $listEmails = [];
        $listEmails = $this->getUserEmail($account, $email, $accorName);

        if ($isStellantis) {
            $listEmails = \array_merge($listEmails, $this->getStellantisEmails($account));
        }
        try {
            foreach ($listEmails as $email) {
                $this->mailerProvider->send(
                    $email['from'],
                    $email['to'],
                    $email['subject'],
                    $this->twig->render($email['template'], $email['params']),
                    $email['options'] ?? []
                );
            }
        } catch (Exception $exception) {
            $this->logger->critical(
                "Erreur d'envoi de mail pour une demande de subscription "
                .$account->getUser()->getemail().' '.$account->getAdherent()->getName().' : '.
                $exception->getMessage()
            );
        }
    }

    private function getUserEmail(Account $account, string $email, ?string $accordName): array
    {
        return [
            [
                'from' => $this->emailFrom,
                'to' => $email,
                'subject' => 'MARKETPLACE - Bénéficier des conditions pour la FAT ' . $accordName,
                'template' => 'mails/request.accord.subscription.html.twig',
                'params' => [
                    'fat' => $accordName,
                    'email' => $account->getUser()->getemail(),
                    'nom' => $account->getUser()->getFirstName() . ' ' . $account->getUser()->getLastName(),
                    'societe' => $account->getAdherent()->getName(),
                    'sugarLink' => $this->sugarLink . $account->getAdherent()->getId(),
                ],
            ],
        ];
    }

    private function getStellantisEmails(Account $account): array
    {
        $parameters = $this->stellantisParams;
        return [
            [
                'from' => $parameters['ADHERENT_MAIL']['FROM'],
                'to' => \explode(';', $parameters['ADHERENT_MAIL']['TO']),
                'subject' => 'Marketplace - ' . $account->getAdherent()->getSiret() . ' - Demande de rattachement au contrat QANTIS/STELLANTIS',
                'template' => 'mails/stellantis/to_adherent_service.html.twig',
                'params' => [
                    'account' => $account,
                    'horodatage' => new \DateTime('now'),
                ],
            ],
            [
                'from' => $parameters['STELLANTIS_MAIL']['FROM'],
                'to' => \explode(';', $parameters['STELLANTIS_MAIL']['TO']),
                'subject' => $account->getAdherent()->getSiret() . ' - Demande de rattachement au contrat STELLANTIS',
                'template' => 'mails/stellantis/to_stellantis.html.twig',
                'params' => [
                    'account' => $account,
                    'horodatage' => new \DateTime('now'),
                ],
                'options' => ['cc' => \explode(',', $parameters['STELLANTIS_MAIL']['CC'])],
            ],
        ];
    }
}
