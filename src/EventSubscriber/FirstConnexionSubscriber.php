<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Events\FirstConnexionEvent;
use App\Service\MailerProvider;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class FirstConnexionSubscriber implements EventSubscriberInterface
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
            FirstConnexionEvent::class => 'onFirstConnexion',
        ];
    }

    public function onFirstConnexion(FirstConnexionEvent $event): void
    {
        $user = $event->getUser();
        $confirmation_url = $this->router->generate(
            'first_signin_action',
            ['token' => $user->getConfirmationToken()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $this->mailerProvider->send(
            $event->getChannel()->getChannelParameter()->getEmail(),
            $user->getEmail(),
            $this->translator->trans('emails.request.first_connexion.subject', [], 'prehome'),
            $this->twig->render('mails/request.first_connexion.html.twig', [
                'username' => $user->getLastName().' '.$user->getFirstName(),
                'confirmation_url' => $confirmation_url,
                'channel' => $event->getChannel(),
            ])
        );
    }
}
