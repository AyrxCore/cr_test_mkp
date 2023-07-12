<?php

namespace App\EventSubscriber;

use App\Entity\UserInfoUpdateRequest;
use App\Events\FirstConnexionEvent;
use App\Events\UserInfoUpdateEvent;
use App\Service\MailerProvider;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Service\Attribute\Required;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;


class UserInfoUpdateSubscriber implements EventSubscriberInterface
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

    #[Required]
    public EntityManagerInterface $em;

    public LoggerInterface $logger;


    public static function getSubscribedEvents(): array
    {
        return [
            UserInfoUpdateEvent::class => 'onUserInfoUpdate',
        ];
    }


    public function onUserInfoUpdate(UserInfoUpdateEvent $event): void
    {
        $user = $event->getUser();
        $adherent = $user->getAccounts()[0]->getAdherent();

        $logEmail = $this->em->getRepository(UserInfoUpdateRequest::class)->findOneBy([
            '_user'     => $user,
            'attribute' => 'email',
            'isIso'     => 'false',
        ]);
        $logLastname = $this->em->getRepository(UserInfoUpdateRequest::class)->findOneBy([
            '_user'     => $user,
            'attribute' => 'lastname',
            'isIso'     => 'false',
        ]);
        $logFirstname = $this->em->getRepository(UserInfoUpdateRequest::class)->findOneBy([
            '_user'     => $user,
            'attribute' => 'firstname',
            'isIso'     => 'false',
        ]);
        $logPhone = $this->em->getRepository(UserInfoUpdateRequest::class)->findOneBy([
            '_user'   => $user,
            'attribute' => 'phone',
            'isIso'     => 'false',
        ]);
        $logs = [
            'email'     => $logEmail,
            'firstname' => $logFirstname,
            'lastname'  => $logLastname,
            'phone'     => $logPhone,
        ];

        $from = $this->parameterBag->get('SUBSCRIPTION_MAIL_FROM');
        $to = $this->parameterBag->get('SUBSCRIPTION_MAIL_TO');
        $sugarLink = $this->parameterBag->get('SUBSCRIPTION_MAIL_SUGAR_LINK');

        $this->mailerProvider->send(
            $from,
            $to,
            'Un utilisateur a changé ses informations personnelles',
            $this->twig->render('mails/request.userinfo.update.html.twig', [
                'objet'        => '[marketplace] Un utilisateur a modifié ses informations personnelles',
                'logs'         => $logs,
                'email'        => $user->getEmail(),
                'firstname'    => $user->getFirstName(),
                'lastname'     => $user->getLastName(),
                'phone'        => $logPhone?->getOldValue(),
                'adherentName' => $adherent->getName(),
                'sugarLink'    => $sugarLink . (string)$adherent->getId(),
            ])
        );
    }

}
