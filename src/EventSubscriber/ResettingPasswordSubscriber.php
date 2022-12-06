<?php

namespace App\EventSubscriber;

use App\Events\ResettingPasswordEvent;
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


class ResettingPasswordSubscriber implements EventSubscriberInterface
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
            ResettingPasswordEvent::class => 'onPasswordReset',
        ];
    }


    public function onPasswordReset(ResettingPasswordEvent $event): void
    {
        $user = $event->getUser();
        $confirmation_url = $this->router->generate(
            'resetting_action',
            ['token' => $user->getConfirmationToken()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $this->mailerProvider->send(
            $this->parameterBag->get('mail_from'),
            $user->getEmail(),
            'resetting_password',
            $this->twig->render('mails/request.resetting.password.html.twig', [
                'username' => $user->getLastName() . " " . $user->getFirstName(),
                'confirmation_url' => $confirmation_url
            ])
        );
    }

}
