<?php

namespace App\EventSubscriber;

use App\Events\FirstConnexionEvent;
use App\Service\MailerProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Service\Attribute\Required;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;


class FirstConnexionSubscriber implements EventSubscriberInterface
{
    #[Required]
    public RouterInterface $router;

    #[Required]
    public MailerProvider $mailerProvider;

    #[Required]
    public ParameterBagInterface $parameterBag;

    #[Required]
    public Environment $twig;

    #[Required]
    public TranslatorInterface $translator;


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
            'resetting_first_connexion_action',
            ['token' => $user->getConfirmationToken()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $this->mailerProvider->send(
            $this->parameterBag->get('mail_from'),
            $user->getEmail(),
            $this->translator->trans('emails.request.first_connexion.subject', [], 'prehome'),
            $this->twig->render('mails/request.first_connexion.html.twig', [
                'username' => $user->getLastName() . " " . $user->getFirstName(),
                'confirmation_url' => $confirmation_url
            ])
        );
    }

}
