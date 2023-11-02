<?php

declare(strict_types=1);

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

    #[Route('/login/reset-password', name: 'reset_password')]
    #[Route('/login/first-signin', name: 'first_signin')]
    public function request(Request $request, EntityManagerInterface $em, TranslatorInterface $translator): Response
    {
        $session = $this->requestStack->getSession();

        if ($request->attributes->get('_route') === 'reset_password') {
            $tpl = 'request';
        } else {
            $tpl = 'first_connexion.request';
        }

        if ($request->getMethod() == 'POST') {
            $username = $request->request->get('_username');
            $user = $em->getRepository(User::class)->findUserByUsernameOrEmail($username);
            if (!empty($user) && $user instanceof User && $user->hasEnabledAccount()) {
                /**
                 * @var User $user
                 */
                if ($user->getPasswordRequestedAt() !== null) {
                    if ($user->getPasswordRequestedAt()->getTimestamp() + $this->getParameter('retry_ttl') > \time()) {
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

                if ($request->attributes->get('_route') === 'reset_password' && !$user->isEnabled()) {
                    $session->getFlashBag()->add(
                        'warning',
                        $translator->trans(
                            'resetting.user.disabled',
                            [],
                            'prehome'
                        )
                    );

                    return $this->redirectToRoute($request->attributes->get('_route'));
                }

                $user->setPasswordRequestedAt(new \DateTime('now'));
                $token = \md5(\random_bytes(100));
                $user->setConfirmationToken($token);

                if ($request->attributes->get('_route') === 'reset_password') {
                    $event = new ResettingPasswordEvent($user);
                    $session->getFlashBag()->add(
                        'success',
                        $translator->trans('resetting.request.success', [], 'prehome')
                    );
                } elseif ($request->attributes->get('_route') == 'first_signin') {
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

        return $this->render('login/'.$tpl.'.html.twig');
    }

    #[Route('/login/reset-password/{token}', name: 'reset_password_action')]
    #[Route('/login/first-signin/{token}', name: 'first_signin_action')]
    public function resetPassword(
        Request $request,
        EntityManagerInterface $em,
        TranslatorInterface $translator,
        string $token
    ) {
        if ($request->attributes->get('_route') === 'reset_password_action') {
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

            if ($request->attributes->get('_route') === 'reset_password_action') {
                return $this->redirectToRoute('reset_password');
            } else {
                return $this->redirectToRoute('first_signin');
            }
        }

        $form = $this->createForm(ResettingType::class, null);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $encodedPassword = $this->passwordHasher->hashPassword($user, $form->get('password')->getData());

            if (
                $request->attributes->get('_route') === 'first_signin_action'
                && $user->getFirstConnexionRequestedAt() !== null
            ) {
                $user->setFirstConnexionRequestedAt(null);
                $user->setIsEnabled(true);
            }

            $user->setPassword($encodedPassword);
            $user->setConfirmationToken(null);
            $user->setPasswordRequestedAt(null);
            $em->persist($user);
            $em->flush();

            if ($request->attributes->get('_route') === 'reset_password_action') {
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

            return $this->redirect('/');
        }

        return $this->render('login/'.$tpl.'.html.twig', ['form' => $form->createView()]);
    }
}
