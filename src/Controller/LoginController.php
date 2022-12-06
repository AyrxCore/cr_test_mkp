<?php

namespace App\Controller;

use App\Entity\User;
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
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
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
    public function request(Request $request, EntityManagerInterface $em, TranslatorInterface $translator): Response
    {
        $session = $this->requestStack->getSession();

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
                        return $this->redirectToRoute('resetting_request');
                    }
                }

                $user->setPasswordRequestedAt(new \DateTime('now'));
                $token = md5(random_bytes(100));
                $user->setConfirmationToken($token);
                $em->persist($user);
                $em->flush();
                $event = new ResettingPasswordEvent($user);
                $this->eventDispatcher->dispatch($event);
                $session->getFlashBag()->add(
                    'success',
                    $translator->trans('resetting.request.success', [], 'prehome')
                );
                return $this->redirectToRoute('resetting_request');
            } else {
                $session->getFlashBag()->add(
                    'warning',
                    $translator->trans('resetting.request.failed', [], 'prehome')
                );
                return $this->redirectToRoute('resetting_request');
            }
        }

        return $this->render('login/request.html.twig');
    }


    #[Route("/resetting/{token}", name:"resetting_action")]
    public function resetPassword(
        Request $request,
        EntityManagerInterface $em,
        TranslatorInterface $translator,
        string $token
    ) {
        $session = new Session();
        $user = $em->getRepository(User::class)->findOneBy(['confirmation_token' => $token]);

        if (empty($user)) {
            $session->getFlashBag()->add(
                'danger',
                $translator->trans('resetting.token.error', [], 'prehome')
            );
            return $this->redirectToRoute('resetting_request');
        }

        $form = $this->createForm(ResettingType::class, null);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $encodedPassword = $this->passwordHasher->hashPassword($user, $form->get('password')->getData());

            $user->setPassword($encodedPassword);
            $user->setConfirmationToken(null);
            $user->setPasswordRequestedAt(null);
            $em->persist($user);
            $em->flush();
            $session->getFlashBag()->add('success', $translator->trans('resetting.success', [], 'prehome'));
            return $this->redirectToRoute('app');
        }

        return $this->render('login/reset.html.twig', ['form' => $form->createView()]);
    }
}
