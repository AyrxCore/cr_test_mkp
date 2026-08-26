<?php

declare(strict_types=1);

namespace App\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\AdherentTarifShowcase;
use App\Entity\User;
use App\Service\MailerProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Twig\Environment;

class AdherentTarifShowcaseContactRequestProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private MailerProvider $mailerProvider,
        private RequestStack $requestStack,
        private Environment $twig,
        private readonly ParameterBagInterface $parameterBag,
        private Security $security,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): AdherentTarifShowcase
    {
        if (!$data instanceof AdherentTarifShowcase) {
            throw new BadRequestHttpException('Invalid data');
        }

        /** @var User $currentUser */
        $currentUser = $this->security->getUser();
        $userAdherentIds = $currentUser->getAccounts()
            ->map(static fn ($account) => $account->getAdherent()?->getId())
            ->filter(static fn ($id) => $id !== null)
            ->toArray();
        if (!\in_array($data->getAdherent()->getId(), $userAdherentIds, true)) {
            throw new AccessDeniedHttpException('You do not have access to this showcase');
        }

        $request = $this->requestStack->getCurrentRequest();
        $content = $request->getContent();
        $decodedContent = \json_decode($content, true);

        if (\json_last_error() !== \JSON_ERROR_NONE) {
            throw new BadRequestHttpException('Invalid JSON');
        }

        $accordName = $decodedContent['accordName'] ?? null;

        if ($data->isContactRequested()) {
            return $data;
        }

        $data->setContactRequested(true);
        $this->entityManager->persist($data);
        $this->entityManager->flush();

        $adherent = $data->getAdherent();

        $subject = 'Demande de recontact - FAT mise en vitrine'.($accordName ? ' - '.$accordName : '');
        $emailData = [
            'accord_name' => $accordName,
            'adherent_name' => $adherent->getName(),
            'accord_id' => $data->getAccordId(),
            'tarif_id' => $data->getTarifId(),
            'sugarLink' => $this->parameterBag->get('ACCOUNTS_SUGAR_LINK').(string) $adherent->getId(),
        ];

        $this->mailerProvider->send(
            $this->parameterBag->get('CONTACT_MAIL_FROM'),
            $this->parameterBag->get('MAIL_CONTACT_SHOWCASE_TO'),
            $subject,
            $this->twig->render('mails/request.adherent_tarif_showcase_open.html.twig', $emailData)
        );

        return $data;
    }
}
