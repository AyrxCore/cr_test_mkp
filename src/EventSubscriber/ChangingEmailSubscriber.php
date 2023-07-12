<?php

namespace App\EventSubscriber;

use App\Entity\UserInfoUpdateRequest;
use App\Events\ChangingEmailEvent;
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


class ChangingEmailSubscriber implements EventSubscriberInterface
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
        $this->mailerProvider->send(
            $this->parameterBag->get('mail_from'),
            $log->getValue(),
            $this->translator->trans('emails.request.changing_email_validation.subject', [], 'prehome'),
            $this->twig->render('mails/request.changing_email_validation.html.twig', [
                'username'         => $user->getLastName() . " " . $user->getFirstName(),
                'confirmation_url' => $confirmation_url,
                'newEmail'         => $log->getValue(),
                'oldEmail'         => $log->getOldValue(),
            ])
        );
        $this->mailerProvider->send(
            $this->parameterBag->get('mail_from'),
            $log->getValue(),
            $this->translator->trans('emails.request.changing_email_information.subject', [], 'prehome'),
            $this->twig->render('mails/request.changing_email_information.html.twig', [
                'username'         => $user->getLastName() . " " . $user->getFirstName(),
                'confirmation_url' => $confirmation_url,
                'newEmail'         => $log->getValue(),
                'oldEmail'         => $log->getOldValue(),
            ])
        );
    }

}
