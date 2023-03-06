<?php

namespace App\Controller\Api\Buyer;

use App\Service\MailerProvider;
use Doctrine\DBAL\Exception\NotNullConstraintViolationException;
use PharIo\Manifest\InvalidEmailException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Service\Attribute\Required;
use Twig\Environment;

class ContactController extends AbstractController
{
    public const LIST_MOTIFS = [
        'Question sur une commande ou un produit',
        'Question sur une livraison',
        'Question sur Mon Compte',
        'Question sur un partenaire ou un accord-cadre',
        'Votre avis sur la marketplace',
        'Autre'
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

    /**
     * @throws \Exception
     */
    #[Route('api/contact/send-email', name: 'send_contact_email', methods: ['POST'])]
    public function sendContact(Request $request): JsonResponse
    {
        $session = $this->requestStack->getSession();

        $session->start();

        $options = $request->request->all();

        if (!$session->has('account') || empty($session->get('account'))) {
            return new JsonResponse('session account is not hydrated', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

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
        $error = false;
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
                $this->logger->critical("Erreur d'envoie de mail " . $contact->email . " : " . $exception->getMessage());
            }
        } else {
            $error = true;
            $message = implode('<br>', $contact->errors);
        }

        return new JsonResponse(['error' => $error, 'message' => $message]);
    }

    #[Route('api/contact/list-motifs', name: 'list_motifs', methods: ['GET'])]
    public function getListMotifs(Request $request): JsonResponse
    {
        $session = $this->requestStack->getSession();

        $session->start();

        if (!$session->has('account') || empty($session->get('account'))) {
            return new JsonResponse('session account is not hydrated', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(self::LIST_MOTIFS);
    }

    private function validateFormContact(\stdClass $contact)
    {
        if (null === $contact->lastName) {
            $contact->errors[] = 'Le nom est obligatoire';
        }

        if (null === $contact->firstName) {
            $contact->errors[] = 'Le prénom est obligatoire';
        }

        if (null === $contact->email) {
            $contact->errors[] = 'L\'email est obligatoire';
        } elseif (!filter_var($contact->email, FILTER_VALIDATE_EMAIL)) {
            $contact->errors[] = 'L\'email ' . $contact->email . ' vous avez saisi n\'est pas correcte';
        }

        if (null === $contact->description) {
            $contact->errors[] = 'La description est obligatoire';
        }


        if (null === $contact->motif) {
            $contact->errors[] = 'La sélection du motif est obligatoire';
        }

        return empty($contact->errors);
    }
}
