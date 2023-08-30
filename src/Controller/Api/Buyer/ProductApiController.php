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
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
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

    private const PAGE = 1;

    private const PER_PAGE = 5;

    #[Required]
    public RequestStack $requestStack;

    #[Required]
    public EntityManagerInterface $em;

    #[Required]
    public UpplerProductService $upplerProductService;

    #[Required]
    public Environment $twig;

    #[Route('/api/products', name: 'search_products', methods: ['POST'])]
    public function list(Request $request, NormalizerInterface $normalizer): JsonResponse
    {
        $options = $request->request->all();

        $showFilters = false;
        $page = self::PAGE;
        $perPage = self::PER_PAGE;

        if (!empty($options['with_filter'])) {
            $showFilters = true;
            unset($options['with_filter']);
        }

        if (!empty($options['page'])) {
            $page = $options['page'];
            unset($options['page']);
        }

        if (!empty($options['perPage'])) {
            $perPage = $options['perPage'];
            unset($options['perPage']);
        }

        $products = $this->upplerProductService->findProductsByOptions(
            $options,
            ['properties', 'price', 'company', 'images'],
            (int) $page,
            (int) $perPage,
            $showFilters
        );

        return new JsonResponse($products);
    }

    #[Route('/api/home-products', name: 'search_home_products', methods: ['GET'])]
    public function homeProduct(): JsonResponse
    {
        $params = ['properties', 'price', 'company', 'images'];

        // TODO: make concurrent requests: https://symfony.com/doc/current/http_client.html#concurrent-requests
        $productsTopVente = $this->upplerProductService->findProductsByOptions(
            ['properties' => [self::HOME_TOP_VENTE_PROPERTY]],
            $params,
            self::PAGE,
            self::PER_PAGE
        );
        $accordsCadre = $this->upplerProductService->findProductsByOptions(
            ['properties' => [self::HOME_ACCORD_CADRE_PROPERTY]],
            ['properties'],
            self::PAGE,
            self::PER_PAGE
        );
        $productsSelection = $this->upplerProductService->findProductsByOptions(
            ['properties' => [self::HOME_SELECTION_PROPERTY]],
            $params,
            self::PAGE,
            self::PER_PAGE
        );
        $products = new \stdClass();
        $products->topVente = $productsTopVente;
        $products->accordsCadre = $accordsCadre;
        $products->selection = $productsSelection;

        return new JsonResponse($products);
    }

    #[Route('/api/categories-list', name: 'categories_list', methods: ['POST'])]
    public function categoriesList(): JsonResponse
    {
        $session = $this->requestStack->getSession();

        $resultat = $this->upplerProductService->findAllCategories((string) $session->get('account')->getId());

        $resultat = \json_decode(\json_encode($resultat), true);

        $categories = (array) $resultat;
        $listMenu = \array_slice($categories, 0, 6);

        \usort($categories, function ($a, $b) {
            return \strcmp($a['name'], $b['name']);
        });

        return new JsonResponse(['categories' => $categories, 'menu' => $listMenu]);
    }

    #[Route('/api/product/{id}', name: 'get_product')]
    public function product(int $id): JsonResponse
    {
        $product = $this->upplerProductService->findProductById($id);

        return new JsonResponse($product);
    }

    #[Route('/api/variant/{id}', name: 'get_variant')]
    public function variant(int $id): JsonResponse
    {
        $variant = $this->upplerProductService->findVariantById($id);

        return new JsonResponse($variant);
    }

    #[Route('/api/accord-cadre/{id}', name: 'get_accord_cadre')]
    public function accordCadre(int $id, NormalizerInterface $normalizer): JsonResponse
    {
        $session = $this->requestStack->getSession();

        $accordCadre = $this->upplerProductService->findProductById(
            $id,
            ['properties', 'company'],
            (string) $session->get('account')->getId()
        );

        return new JsonResponse($accordCadre);
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
