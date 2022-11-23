<?php
namespace App\Security;

use App\Entity\User;
use App\Service\UpplerAuthenticationService;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authenticator\JWTAuthenticator;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\Service\Attribute\Required;

class UpplerAuthenticator extends JWTAuthenticator
{
    #[Required]
    public UpplerAuthenticationService $upplerAuthenticationService;

    #[Required]
    public JWTTokenManagerInterface $JWTTokenManager;

    #[Required]
    public RequestStack $requestStack;

    #[Required]
    public EntityManagerInterface $em;

    #[Required]
    public UserPasswordHasherInterface $userPasswordHasher;

    // Your own logic
    public function supports(Request $request): ?bool
    {
        return (
            ($request->attributes->get('_route') === 'authentication_token') ||
            (false !== $this->getTokenExtractor()->extract($request))
        );
    }

    public function authenticate(Request $request): Passport
    {
        $session = $this->requestStack->getSession();
        $session->start();
        $datas = json_decode($request->getContent());

        if ((is_object($datas)) &&
            (property_exists($datas, 'username') ||
            property_exists($datas, 'password') ||
            (!empty($datas->username) || !empty($datas->password)))
        ) {
            $userAuth = $this->upplerAuthenticationService->authenticateUser($datas->username, $datas->password);

            if ($userAuth && $session->has('access_token') && !empty($session->get('access_token'))) {
                /**@var User $user*/
                $user = $this->em->getRepository(User::class)->findOneBy(['username' => $datas->username]);
                if (!$user) {
                    throw new CustomUserMessageAuthenticationException('User unkown from UPPLER');
                }
                // on update le password user avec le password Uppler,
                // nécessaire pour que lexik considère l'authentification ok
                // on ne persist pas en database car pas nécessaire de stocker ce password il est déjà chez Uppler
                $user->setPassword($this->userPasswordHasher->hashPassword($user, $datas->password));
                $token = $this->JWTTokenManager->create($user);

                $passport = new SelfValidatingPassport(
                    new UserBadge($datas->username, function (string $userIdentifier) {
                        return $this->em->getRepository(User::class)->findOneBy(['username' => $userIdentifier]);
                    })
                );
                $passport->setAttribute('payload', []);
                $passport->setAttribute('token', $token);
                return $passport;
            }
            throw new CustomUserMessageAuthenticationException('Authentication Error');
        } else {
            return parent::doAuthenticate($request);
        }
    }

}
