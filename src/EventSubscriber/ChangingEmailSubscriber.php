<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\UserInfoUpdateRequest;
use App\Events\ChangingEmailEvent;
use App\Service\MailerProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class ChangingEmailSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private RouterInterface $router,
        private MailerProvider $mailerProvider,
        private Environment $twig,
        private TranslatorInterface $translator,
        private EntityManagerInterface $em,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ChangingEmailEvent::class => 'onChangingEmail',
        ];
    }

    public function onChangingEmail(ChangingEmailEvent $event): void
    {
        $user = $event->getUser();

        $log = $this->em->getRepository(UserInfoUpdateRequest::class)->findOneBy(
            ['attribute' => 'email', '_user' => $user, 'isIso' => false]
        );

        $confirmation_url = $this->router->generate(
            'changing_email_action',
            ['token' => $log->getEmailChangingToken()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $channelEmailTo = $event->getChannel()->getChannelParameter()->getEmailFrom();
        $this->mailerProvider->send(
            $channelEmailTo,
            $log->getValue(),
            $this->translator->trans('emails.request.changing_email_validation.subject', [], 'prehome'),
            $this->twig->render('mails/request.changing_email_validation.html.twig', [
                'username' => $user->getLastName().' '.$user->getFirstName(),
                'confirmation_url' => $confirmation_url,
                'newEmail' => $log->getValue(),
                'oldEmail' => $log->getOldValue(),
                'channel' => $event->getChannel(),
            ])
        );
        $this->mailerProvider->send(
            $channelEmailTo,
            $log->getOldValue(),
            $this->translator->trans('emails.request.changing_email_information.subject', [], 'prehome'),
            $this->twig->render('mails/request.changing_email_information.html.twig', [
                'username' => $user->getLastName().' '.$user->getFirstName(),
                'confirmation_url' => $confirmation_url,
                'newEmail' => $log->getValue(),
                'oldEmail' => $log->getOldValue(),
                'channel' => $event->getChannel(),
            ])
        );
    }
}
