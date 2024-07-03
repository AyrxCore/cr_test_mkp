<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Events\FirstConnexionEvent;
use App\Events\ResettingPasswordEvent;
use App\Exception\AutoLoginException;
use App\Form\ResettingType;
use App\Repository\AccountRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class LoginController extends AbstractController implements ChannelAwareControllerInterface
{
    use ChannelAwareControllerTrait;

    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private RequestStack $requestStack,
        private UserPasswordHasherInterface $passwordHasher,
        private UserRepository $userRepository,
        private AccountRepository $accountRepository,
    ) {
    }

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

        $channel = $this->getChannel($request);
        $routeParams = [
            'channel' => $channel,
        ];

        if ($request->getMethod() == 'POST') {
            $username = $request->getPayload()->all()['_username'];
            $user = $em->getRepository(User::class)->findUserByUsernameOrEmail($username);
            if (!empty($user) && $user instanceof User && (bool) $user->getFirstEnabledAccount($channel)) {
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

                        return $this->redirectToRoute($request->attributes->get('_route'), $routeParams);
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

                    return $this->redirectToRoute($request->attributes->get('_route'), $routeParams);
                }

                $user->setPasswordRequestedAt(new \DateTime('now'));
                $token = \md5(\random_bytes(100));
                $user->setConfirmationToken($token);

                $event = null;
                if ($request->attributes->get('_route') === 'reset_password') {
                    $event = new ResettingPasswordEvent($user, $channel);
                    $session->getFlashBag()->add(
                        'success',
                        $translator->trans('resetting.request.success', [], 'prehome')
                    );
                } elseif ($request->attributes->get('_route') == 'first_signin') {
                    $user->setFirstConnexionRequestedAt(new \DateTime('now'));
                    $event = new FirstConnexionEvent($user, $channel);
                    $session->getFlashBag()->add(
                        'success',
                        $translator->trans('resetting.request.first_connexion.success', [], 'prehome')
                    );
                }

                $em->persist($user);
                $em->flush();

                if ($event) {
                    $this->eventDispatcher->dispatch($event);
                }

                return $this->redirectToRoute($request->attributes->get('_route'), $routeParams);
            } else {
                $session->getFlashBag()->add(
                    'warning',
                    $translator->trans('resetting.request.failed', [], 'prehome')
                );

                return $this->redirectToRoute($request->attributes->get('_route'), $routeParams);
            }
        }

        return $this->render(\sprintf('login/%s.html.twig', $tpl), [
            'channel' => $this->getChannel($request),
        ]);
    }

    #[Route('/login/reset-password/{token}', name: 'reset_password_action')]
    #[Route('/login/first-signin/{token}', name: 'first_signin_action')]
    public function resetPassword(
        Request $request,
        EntityManagerInterface $em,
        TranslatorInterface $translator,
        string $token
    ) {
        $session = new Session();
        $user = $em->getRepository(User::class)->findOneBy(['confirmation_token' => $token]);
        $channel = $this->getChannel($request);
        $passwordChanged = false;

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
                $user->setEnabled(true);
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

            $passwordChanged = true;
        }

        return $this->render('login/reset.html.twig', [
            'form' => $form->createView(),
            'channel' => $channel,
            'passwordChanged' => $passwordChanged,
        ]);
    }

    #[Route('/login/auto-login', name: 'generate_auto_login_link')]
    public function generateAutoLoginLink(
        Request $request,
        LoginLinkHandlerInterface $loginLinkHandler,
    ): JsonResponse {
        if ($request->isMethod('GET')) {
            $hash = $request->query->get('hash');
            $timestamp = (int) $request->query->get('timestamp');
            $email = $request->query->get('email');
            $user = $this->userRepository->findOneBy(['email' => $email]);

            if (!$user) {
                throw new AutoLoginException('email not found');
            }

            if (!$channel = $this->getChannel($request)) {
                throw new BadRequestException();
            }

            if (!$account = $user->getFirstEnabledAccount($channel)) {
                throw new BadRequestException('No account available');
            }

            if ($timestamp < \time() + 60 && $timestamp > \time() - (60 * 60)) {
                $hashkey = $account->getAdherent()?->getHashkey() ?: '';
                $data = $email.$timestamp.$hashkey;
                if (\base64_encode(\hash('sha256', $data)) !== $hash) {
                    throw new BadRequestException();
                }

                $loginLinkDetails = $loginLinkHandler->createLoginLink($user);

                return new JsonResponse([
                    'url' => $loginLinkDetails->getUrl(),
                ]);
            } else {
                throw new BadRequestException();
            }
        }

        throw new NotFoundHttpException();
    }

    // Generate an auto-login link to allow Qantis user to connect to an adherent's MKP
    // Only for ROLE_API
    #[Route('/login/neo-auto-login', name: 'generate_neo_auto_login_link')]
    public function generateNeoAutoLoginLink(
        Request $request,
        LoginLinkHandlerInterface $loginLinkHandler
    ): JsonResponse {
        if ($request->isMethod('GET')) {
            $contactId = $request->query->get('contactId');
            $account = $this->accountRepository->findOneBy(['contactId' => $contactId, 'enabled' => true]);

            if (!$account) {
                throw new BadRequestException('No account available or account deactivated');
            }

            $user = $account->getUser();
            $loginLinkDetails = $loginLinkHandler->createLoginLink($user);

            return new JsonResponse([
                'url' => $loginLinkDetails->getUrl().'&neoAutoLogin=true',
            ]);
        }

        throw new NotFoundHttpException();
    }
}
