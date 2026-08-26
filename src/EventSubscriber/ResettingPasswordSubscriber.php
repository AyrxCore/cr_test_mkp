<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Events\ResettingPasswordEvent;
use App\Service\MailerProvider;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class ResettingPasswordSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private RouterInterface $router,
        private MailerProvider $mailerProvider,
        private Environment $twig,
        private TranslatorInterface $translator,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ResettingPasswordEvent::class => 'onPasswordReset',
        ];
    }

    public function onPasswordReset(ResettingPasswordEvent $event): void
    {
        $user = $event->getUser();
        $confirmation_url = $this->router->generate(
            'reset_password_action',
            ['token' => $user->getConfirmationToken()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $this->mailerProvider->send(
            $event->getChannel()->getChannelParameter()->getEmailFrom(),
            $user->getEmail(),
            $this->translator->trans('emails.request.resetting.subject', [], 'prehome'),
            $this->twig->render('mails/request.resetting.password.html.twig', [
                'username' => $user->getLastName().' '.$user->getFirstName(),
                'confirmation_url' => $confirmation_url,
                'channel' => $event->getChannel(),
            ])
        );
    }
}
