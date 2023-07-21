<?php

declare(strict_types=1);

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\Service\Attribute\Required;

class MailerProvider
{
    #[Required]
    public EntityManagerInterface $entityManager;

    #[Required]
    public RequestStack $requestStack;

    #[Required]
    public UserPasswordHasherInterface $passwordHasher;

    #[Required]
    public MailerInterface $mailer;

    #[Required]
    public LoggerInterface $logger;

    // options permet d'overrider des paramétres par défaut
    // (sendAsText => passer un contenu texte brut,
    // cc => passer un destinataire en copie,
    // bcc => passer un destinataire en copie caché,
    // replyTo => Passer un email en replyto,
    // priority => passer un degré de priorité,
    // attachments => passer un fichier ou un tableau de fichiers à envoyer
    // avec le chemin d'accès complet sur le serveur)
    public function send(string $from, string $to, string $subject, string $body, array $options = []): bool
    {
        $email = new Email();
        $email
            ->from($from)
            ->returnPath($from)
            ->replyTo($from)
            ->to($to)
            ->subject($subject)
            ->text($body);

        if (!isset($options['sendAsText'])) {
            $email->html($body);
        }

        if (isset($options['cc'])) {
            $email->cc($options['cc']);
        }

        if (isset($options['bcc'])) {
            $email->bcc($options['bcc']);
        }

        if (isset($options['replyTo'])) {
            $email->replyTo($options['replyTo']);
        }

        // Priorité, de Urgent (1) à Non prioritaire (5), par défaut Normal (3) (header "X-Priority")
        if (isset($options['priority'])) {
            $email->priority((int) $options['priority']);
        }

        // Chemin(s) vers la ou les PJ a intégrer
        if (isset($options['attachments'])) {
            if (!\is_array($options['attachments'])) {
                $options['attachments'] = [$options['attachments']];
            }
            foreach ($options['attachments'] as $attachment) {
                $email->attachFromPath($attachment);
            }
        }

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $exception) {
            $this->logger->critical('Email non envoyé à '.$to.' : '.$exception->getMessage());

            return false;
        }

        return true;
    }
}
