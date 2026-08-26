<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Account;
use App\Entity\Channel;
use App\Entity\User;
use App\Events\FirstConnexionEvent;
use App\Events\ResettingPasswordEvent;
use App\Exception\AutoLoginException;
use App\Form\ResettingType;
use App\Repository\AccountRepository;
use App\Repository\UserRepository;
use App\Service\LogAutoLoginErrorService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
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
        private readonly LogAutoLoginErrorService $logAutoLoginErrorService,
        private readonly LoggerInterface $logger,
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
            $username = $request->getPayload()->all()['_username'] ?? null;
            if (!\is_string($username) || \trim($username) === '') {
                $this->logger->warning('Login request received without _username in payload.', [
                    'route' => $request->attributes->get('_route'),
                    'ip' => $request->getClientIp(),
                ]);

                return $this->redirectToRoute($request->attributes->get('_route'), $routeParams);
            }
            $user = $em->getRepository(User::class)->findUserByUsernameOrEmail($username, $channel);
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
            }
            $session->getFlashBag()->add(
                'warning',
                $translator->trans('resetting.request.failed', [], 'prehome')
            );

            return $this->redirectToRoute($request->attributes->get('_route'), $routeParams);
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
        string $token,
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
            }

            return $this->redirectToRoute('first_signin');
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

            if (!$email || !$hash || !$timestamp) {
                throw new AutoLoginException('Missing parameters (Email or timestamp or hash)');
            }

            $user = $this->userRepository->findOneBy(['email' => $email]);

            if (!$user) {
                $this->logAutoLoginError($this->getChannel($request), $email, 'Adresse email non trouvé dans la base de données');
                throw new AutoLoginException('email not found');
            }

            if (!$channel = $this->getChannel($request)) {
                $this->logAutoLoginError($this->getChannel($request), $email, 'Aucun channel trouvé pour cette requête');
                throw new AutoLoginException('channel not found');
            }

            if (!$account = $user->getFirstEnabledAccount($channel)) {
                $this->logAutoLoginError($this->getChannel($request), $email, 'Aucun compte valide trouvé');
                throw new AutoLoginException('No account available');
            }

            if (!$this->isTimestampValid($timestamp)) {
                $this->logAutoLoginError($this->getChannel($request), $email, 'Le temps de validité du lien de connexion a expiré');
                throw new AutoLoginException('Link expired');
            }

            if (!$this->isHashValid($account, $hash, $email, $timestamp)) {
                $this->logAutoLoginError($this->getChannel($request), $email, 'Hash invalide');
                throw new AutoLoginException('Invalid hash');
            }

            $loginLinkDetails = $loginLinkHandler->createLoginLink($user);

            return new JsonResponse([
                'url' => $loginLinkDetails->getUrl(),
            ]);
        }

        throw new NotFoundHttpException();
    }

    // Generate an auto-login link to allow Qantis user to connect to an adherent's MKP
    // Only for ROLE_API
    #[Route('/login/neo-auto-login', name: 'generate_neo_auto_login_link')]
    public function generateNeoAutoLoginLink(
        Request $request,
        LoginLinkHandlerInterface $loginLinkHandler,
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
                'url' => $loginLinkDetails->getUrl().'&neoAutoLogin=true&contactId='.$contactId,
            ]);
        }

        throw new NotFoundHttpException();
    }

    private function isTimestampValid(?int $timestamp): bool
    {
        $currentTime = \time();
        $timeWindow = 60 * 60; // 1 heure en secondes

        return $timestamp < $currentTime + 60 && $timestamp > $currentTime - $timeWindow;
    }

    private function isHashValid(Account $account, string $hash, string $email, int $timestamp): bool
    {
        $hashkey = $account->getAdherent()?->getHashkey() ?: '';
        $data = $email.$timestamp.$hashkey;

        return \base64_encode(\hash('sha256', $data)) === $hash;
    }

    private function logAutoLoginError(?Channel $channel, string $email, string $reason): void
    {
        $this->logAutoLoginErrorService->log($channel?->getName(), $email, $reason);
    }
}
