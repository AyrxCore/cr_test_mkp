<?php

namespace App\Controller;

use App\Entity\User;
use App\Events\FirstConnexionEvent;
use App\Events\ResettingPasswordEvent;
use App\Form\ResettingType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Service\Attribute\Required;
use Symfony\Contracts\Translation\TranslatorInterface;

class LoginController extends AbstractController
{
    #[Required]
    public RequestStack $requestStack;

    #[Required]
    public EventDispatcherInterface $eventDispatcher;

    #[Required]
    public UserPasswordHasherInterface $passwordHasher;

    #[Route('/mot-de-passe-oublie', name: 'resetting_request')]
    #[Route('/premiere-connexion', name: 'first_connexion_request')]
    public function request(Request $request, EntityManagerInterface $em, TranslatorInterface $translator): Response
    {
        $session = $this->requestStack->getSession();

        if ($request->attributes->get('_route') === 'resetting_request') {
            $tpl = 'request';
        } else {
            $tpl = 'first_connexion.request';
        }

        if ($request->getMethod() == 'POST') {
            $username = $request->request->get('_username');
            $user = $em->getRepository(User::class)->findUserByUsernameOrEmail($username);
            if (!empty($user) && $user instanceof User) {
                /**
                 * @var User $user
                 */
                if ($user->getPasswordRequestedAt() !== null) {
                    if ($user->getPasswordRequestedAt()->getTimestamp() + $this->getParameter('retry_ttl') > time()) {
                        $session->getFlashBag()->add(
                            'warning',
                            $translator->trans(
                                'resetting.ttl_not_reached',
                                ['%duration%' => $this->getParameter('retry_ttl')],
                                'prehome'
                            )
                        );
                        return $this->redirectToRoute($request->attributes->get('_route'));
                    }
                }

                $user->setPasswordRequestedAt(new \DateTime('now'));
                $token = md5(random_bytes(100));
                $user->setConfirmationToken($token);

                if ($request->attributes->get('_route') === 'resetting_request') {
                    $event = new ResettingPasswordEvent($user);
                    $session->getFlashBag()->add(
                        'success',
                        $translator->trans('resetting.request.success', [], 'prehome')
                    );
                } elseif ($request->attributes->get('_route') == 'first_connexion_request') {
                    $user->setFirstConnexionRequestedAt(new \DateTime('now'));
                    $event = new FirstConnexionEvent($user);
                    $session->getFlashBag()->add(
                        'success',
                        $translator->trans('resetting.request.first_connexion.success', [], 'prehome')
                    );
                }

                $em->persist($user);
                $em->flush();
                $this->eventDispatcher->dispatch($event);

                return $this->redirectToRoute($request->attributes->get('_route'));
            } else {
                $session->getFlashBag()->add(
                    'warning',
                    $translator->trans('resetting.request.failed', [], 'prehome')
                );
                return $this->redirectToRoute($request->attributes->get('_route'));
            }
        }

        return $this->render('login/' . $tpl . '.html.twig');
    }


    #[Route("/mot-de-passe-oublie/{token}", name:"resetting_action")]
    #[Route("/premiere-connexion/{token}", name:"resetting_first_connexion_action")]
    public function resetPassword(
        Request $request,
        EntityManagerInterface $em,
        TranslatorInterface $translator,
        string $token
    ) {
        if ($request->attributes->get('_route') === 'resetting_action') {
            $tpl = 'reset';
        } else {
            $tpl = 'first_connexion.reset';
        }

        $session = new Session();
        $user = $em->getRepository(User::class)->findOneBy(['confirmation_token' => $token]);

        if (empty($user)) {
            $session->getFlashBag()->add(
                'danger',
                $translator->trans('resetting.token.error', [], 'prehome')
            );

            if ($request->attributes->get('_route') === 'resetting_action') {
                return $this->redirectToRoute('resetting_request');
            } else {
                return $this->redirectToRoute('first_connexion_request');
            }
        }

        $form = $this->createForm(ResettingType::class, null);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $encodedPassword = $this->passwordHasher->hashPassword($user, $form->get('password')->getData());

            if ($request->attributes->get('_route') === 'resetting_first_connexion_action' &&
                null !== $user->getFirstConnexionRequestedAt()
            ) {
                $user->setFirstConnexionRequestedAt(null);
                $user->setEnabled(true);
            }

            $user->setPassword($encodedPassword);
            $user->setConfirmationToken(null);
            $user->setPasswordRequestedAt(null);
            $em->persist($user);
            $em->flush();

            if ($request->attributes->get('_route') === 'resetting_action') {
                $session->getFlashBag()->add('success', $translator->trans('resetting.success', [], 'prehome'));
            } else {
                $session->getFlashBag()->add(
                    'success',
                    $translator->trans(
                        'resetting.first_connexion.success',
                        [],
                        'prehome'
                    )
                );
            }
            return $this->redirectToRoute('app');
        }

        return $this->render('login/' . $tpl . '.html.twig', ['form' => $form->createView()]);
    }
}
