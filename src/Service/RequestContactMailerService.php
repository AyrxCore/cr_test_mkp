<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Account;
use Psr\Log\LoggerInterface;
use Twig\Environment;

class RequestContactMailerService
{
    public function __construct(
        private MailerProvider $mailerProvider,
        private LoggerInterface $logger,
        private Environment $twig,
        private string $emailFrom,
        private string $sugarLink,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function sendMail(
        Account $account,
        string $email,
        array $params,
    ): void {
        $listEmails = $this->getUserEmail($account, $email, $params);

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
        } catch (\Exception $exception) {
            $this->logger->critical(
                "Erreur d'envoi de mail pour une demande de subscription "
                .$account->getUser()->getEmail().' '.$account->getAdherent()->getName().' : '.
                $exception->getMessage()
            );
        }
    }

    private function getUserEmail(Account $account, string $email, array $params): array
    {
        return [
            [
                'from' => $this->emailFrom,
                'to' => $email,
                'subject' => 'Demande de prise de contact',
                'template' => 'mails/request.contact.not.sellable.product.html.twig',
                'params' => [
                    'fat' => $params['accordName'],
                    'email' => $account->getUser()->getEmail(),
                    'nom' => $account->getUser()->getFirstName().' '.$account->getUser()->getLastName(),
                    'societe' => $account->getAdherent()->getName(),
                    'sugarLink' => $this->sugarLink.$account->getAdherent()->getId(),
                    'phone' => $params['phone'],
                    'partner' => $params['partner'],
                    'product' => $params['product'],
                    'message' => isset($params['message']) && $params['message'] !== '' ? $params['message'] : null,
                ],
            ],
        ];
    }
}
