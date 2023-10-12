<?php

declare(strict_types=1);

namespace App\Controller\Api\Buyer;

use App\Dto\AccountAccordCadre;
use App\Entity\AccordStatut;
use App\Entity\Account;
use App\Entity\LogAccordStatutRequest;
use App\Service\MailerProvider;
use App\Service\UpplerProductService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\Service\Attribute\Required;
use Twig\Environment;

class ProductApiController extends AbstractController
{
    public const HOME_TOP_VENTE_PROPERTY
        = [
            'property_id' => '217',
            'value' => '5369',
        ];

    public const HOME_SELECTION_PROPERTY
        = [
            'property_id' => '217',
            'value' => '5368',
        ];

    public const HOME_ACCORD_CADRE_PROPERTY
        = [
            'property_id' => '217',
            'value' => '5367',
        ];

    public const PAGE = 1;

    public const PER_PAGE = 5;

    #[Required]
    public RequestStack $requestStack;

    #[Required]
    public EntityManagerInterface $em;

    #[Required]
    public UpplerProductService $upplerProductService;

    #[Required]
    public Environment $twig;

    #[Route('/api/variant/{id}', name: 'get_variant')]
    public function variant(int $id): JsonResponse
    {
        $variant = $this->upplerProductService->findVariantById($id);

        return new JsonResponse($variant);
    }

    #[Route('/api/accord-cadre-subscription', name: 'accord_cadre_subscription', methods: ['POST'])]
    public function subscription(
        Request $request,
        MailerProvider $mailerProvider,
        LoggerInterface $logger,
    ): JsonResponse {
        $session = $this->requestStack->getSession();

        $params = \json_decode($request->getContent(), true);
        $accountId = (string) $session->get('account')->getId();
        $account = $this->em->getRepository(Account::class)->find($accountId);

        $accordStatut = $this->em->getRepository(AccordStatut::class)->findOneBy([
            'adherent' => $account->getAdherent()->getId(),
            'accordId' => $params['accordId'],
        ]);

        $error = false;
        try {
            $sugarLink = $this->getParameter('SUBSCRIPTION_MAIL_SUGAR_LINK');
            $from = $this->getParameter('SUBSCRIPTION_MAIL_FROM');
            $to = $this->getParameter('SUBSCRIPTION_MAIL_TO');

            $mailerProvider->send(
                $from,
                $to,
                'MARKETPLACE - Bénéficier des conditions pour la FAT '.$params['accordName'],
                $this->twig->render('mails/request.accord.subscription.html.twig', [
                    'fat' => $params['accordName'],
                    'email' => $account->getUser()->getemail(),
                    'nom' => $account->getUser()->getFirstName().' '.$account->getUser()->getLastName(),
                    'societe' => $account->getAdherent()->getName(),
                    'sugarLink' => $sugarLink.$account->getAdherent()->getId(),
                ])
            );

            // STELLANTIS
            $parameters = $this->getParameter('STELLANTIS_MAILING');
            if (\in_array($params['accordId'], $parameters['ACCORDS_IDS'], true)) {
                // send adherent service mail
                $mailerProvider->send(
                    $parameters['ADHERENT_MAIL']['FROM'],
                    \explode(';', $parameters['ADHERENT_MAIL']['TO']),
                    'Marketplace - ' . $account->getAdherent()->getSiret() . ' - Demande de rattachement au contrat QANTIS/STELLANTIS',
                    $this->twig->render('mails/stellantis/to_adherent_service.html.twig', [
                        'account' => $account,
                        'horodatage' => new \DateTime('now'),
                    ]),
                );

                // send Stellantis mail
                $mailerProvider->send(
                    $parameters['STELLANTIS_MAIL']['FROM'],
                    \explode(';', $parameters['STELLANTIS_MAIL']['TO']),
                    $account->getAdherent()->getSiret().' - Demande de rattachement au contrat STELLANTIS',
                    $this->twig->render('mails/stellantis/to_stellantis.html.twig', [
                        'account' => $account,
                        'horodatage' => new \DateTime('now'),
                    ]),
                    ['cc' => \explode(',', $parameters['STELLANTIS_MAIL']['CC'])],
                );
            }
        } catch (\Exception $exception) {
            $error = true;
            $logger->critical(
                "Erreur d'envoi de demande de subscription "
                .$account->getUser()->getemail().' '.$account->getAdherent()->getName().' : '.
                $exception->getMessage()
            );
        }
        if ($error) {
            $accordStatut = new AccordStatut();
            $accordStatut->setAdherent($account->getAdherent());
            $accordStatut->setAccordId(new Uuid($params['accordId']));
            $accordStatut->setStatus(AccountAccordCadre::PROCESS_STATUS_NOT_ACTIVATED);
            $accordStatut->setAccordStatutRequestAt(new \DateTime('now'));
        } elseif ($accordStatut) {
            if ($accordStatut->getStatus() === AccountAccordCadre::PROCESS_STATUS_NOT_ACTIVATED) {
                $accordStatut->setStatus(AccountAccordCadre::PROCESS_STATUS_PENDING);

                $this->em->persist($accordStatut);
                $this->em->flush();
            }
        } else {
            $accordStatut = new AccordStatut();
            $accordStatut->setAdherent($account->getAdherent());
            $accordStatut->setAccordId(new Uuid($params['accordId']));
            $accordStatut->setStatus(AccountAccordCadre::PROCESS_STATUS_PENDING);
            $accordStatut->setAccordStatutRequestAt(new \DateTime('now'));
            $this->em->persist($accordStatut);

            $log = new LogAccordStatutRequest();
            $log->setAccordId(new Uuid($params['accordId']));
            $log->setAccount($account);
            $log->setCreatedAt(new \DateTimeImmutable('now'));
            $this->em->persist($log);

            $this->em->flush();
        }

        return new JsonResponse($accordStatut->getStatus());
    }
}
