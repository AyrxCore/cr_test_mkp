<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\ChannelAwareControllerInterface;
use App\Controller\ChannelAwareControllerTrait;
use App\Service\MailerProvider;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

#[Route('/api/contact')]
class ContactController extends AbstractController implements ChannelAwareControllerInterface
{
    use ChannelAwareControllerTrait;

    public const array LIST_MOTIFS = [
        'Question sur une commande ou un produit',
        'Question sur une livraison',
        'Question sur Mon Compte',
        'Question sur un partenaire ou un accord-cadre',
        'Votre avis sur la marketplace',
        'Autre',
    ];

    public function __construct(
        private readonly Environment $twig,
        private readonly LoggerInterface $logger,
        private readonly MailerProvider $mailerProvider,
        private readonly ParameterBagInterface $parameterBag,
        private readonly RequestStack $request,
    ) {
    }

    /**
     * @throws \Exception
     */
    #[Route('/send-email', name: 'send_contact_email', methods: ['POST'])]
    public function sendContact(Request $request): JsonResponse
    {
        $options = \json_decode($request->getContent(), true);

        $error = false;

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
                    $this->parameterBag->get('CONTACT_MAIL_FROM'),
                    $this->getChannel($request)->getChannelParameter()->getEmail(),
                    $contact->motif,
                    $this->twig->render('mails/request.send.contact.html.twig', [
                        'contact' => $contact,
                    ])
                );
                $message = 'Votre demande a bien été envoyée, notre équipe fait le nécessaire pour vous répondre le plus rapidement possible.';
            } catch (\Exception $exception) {
                $error = true;
                $message = 'Un incident est survenu lors de l\'envoi du mail, veuillez essayer ultérieurement';
                $this->logger->critical("Erreur d'envoie de mail ".$contact->email.' : '.$exception->getMessage());
            }
        } else {
            $error = true;
            $message = \implode('<br>', $contact->errors);
        }

        return new JsonResponse(['error' => $error, 'message' => $message]);
    }

    #[Route('/list-motifs', name: 'list_motifs', methods: ['GET'])]
    public function getListMotifs(): JsonResponse
    {
        return new JsonResponse(self::LIST_MOTIFS);
    }

    private function validateFormContact(\stdClass $contact): bool
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
