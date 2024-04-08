<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Service\MailerProvider;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Contracts\Service\Attribute\Required;
use Twig\Environment;

#[Route('/api/contact')]
class ContactController extends AbstractController
{
    public const LIST_MOTIFS = [
        'Question sur une commande ou un produit',
        'Question sur une livraison',
        'Question sur Mon Compte',
        'Question sur un partenaire ou un accord-cadre',
        'Votre avis sur la marketplace',
        'Autre',
    ];

    #[Required]
    public RequestStack $requestStack;

    #[Required]
    public MailerProvider $mailerProvider;

    #[Required]
    public Environment $twig;

    #[Required]
    public ParameterBagInterface $parameterBag;
    public LoggerInterface $logger;

    public function __construct(private CsrfTokenManagerInterface $csrfTokenManager)
    {
    }

    /**
     * @throws \Exception
     */
    #[Route('/send-email', name: 'send_contact_email', methods: ['POST'])]
    public function sendContact(Request $request): JsonResponse
    {
        $options = $request->request->all();

        $token = new CsrfToken('contact_form', $options['_token']);
        $error = false;

        if (!$this->csrfTokenManager->isTokenValid($token)) {
            $error = true;
            $message = 'Le jeton CSRF est invalide.';
        } else {
            $contact = new \stdClass();

            $contact->lastName = $options['lastName'] ?? null;
            $contact->phone = $options['phone'] ?? null;
            $contact->motif = self::LIST_MOTIFS[$options['motif']] ?? null;
            $contact->description = $options['description'] ?? null;
            $contact->email = $options['email'] ?? null;
            $contact->firstName = $options['firstName'] ?? null;
            $contact->companyName = $options['companyName'] ?? null;
            $contact->accordCadreName = $options['accordCadreName'] ?? null;
            $contact->errors = [];
            $isValid = $this->validateFormContact($contact);
            if ($isValid) {
                try {
                    $this->mailerProvider->send(
                        $contact->email,
                        $this->parameterBag->get('mail_contact'),
                        $contact->motif,
                        $this->twig->render('mails/request.send.contact.html.twig', [
                            'contact' => $contact,
                        ])
                    );
                    $message = 'Votre demande a bien été envoyée, <br />notre équipe fait le nécessaire pour vous répondre le plus rapidement';
                } catch (\Exception $exception) {
                    $error = true;
                    $message = 'Un incident est survenu lors de l\'envoie du mail, veuillez essayer ultérieurement';
                    $this->logger->critical("Erreur d'envoie de mail ".$contact->email.' : '.$exception->getMessage());
                }
            } else {
                $error = true;
                $message = \implode('<br>', $contact->errors);
            }
        }

        return new JsonResponse(['error' => $error, 'message' => $message]);
    }

    #[Route('/list-motifs', name: 'list_motifs', methods: ['GET'])]
    public function getListMotifs(): JsonResponse
    {
        return new JsonResponse(self::LIST_MOTIFS);
    }

    #[Route('/token', name: 'contact_token', methods: ['GET'])]
    public function getToken(): JsonResponse
    {
        $token = $this->csrfTokenManager->getToken('contact_form');

        return new JsonResponse($token->getValue());
    }

    private function validateFormContact(\stdClass $contact)
    {
        if ($contact->lastName === null) {
            $contact->errors[] = 'Le nom est obligatoire';
        }

        if ($contact->firstName === null) {
            $contact->errors[] = 'Le prénom est obligatoire';
        }

        if ($contact->email === null) {
            $contact->errors[] = 'L\'email est obligatoire';
        } elseif (!\filter_var($contact->email, \FILTER_VALIDATE_EMAIL)) {
            $contact->errors[] = 'L\'email '.$contact->email.' que vous avez saisi n\'est pas correcte';
        }

        if ($contact->description === null) {
            $contact->errors[] = 'La description est obligatoire';
        }

        if ($contact->motif === null) {
            $contact->errors[] = 'La sélection du motif est obligatoire';
        }

        return empty($contact->errors);
    }
}
