<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Mail\MailerService;
use App\Mail\MailListTemplate;
use App\Repository\UserRepository;
use App\Repository\SettingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;
use Symfony\Contracts\Service\Attribute\Required;

class UserService
{
    private const URL_RESET_PASSWORD = '/reset-password/';
    private const MAX_DAYS_LIFE_REQUEST_PASSWORD = 2;

    #[Required]
    public EntityManagerInterface $entityManager;

    #[Required]
    public RequestStack $requestStack;

    #[Required]
    public UserPasswordHasherInterface $passwordHasher;

    #[Required]
    public UserRepository $userRepository;


    public function addUser(string $email, string $password): void
    {
        $user = new User();
        $user->setUsername($email);
        $user->setEmail($email);
        $user->setEnabled(true);
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }


    /**
     * @throws Exception
     */
    public function changePassword(string $email, string $password): void
    {
        $user = $this->userRepository->getUserByEmail($email);

        if (!$user) {
            throw new Exception('User not found');
        }

        $user->setPassword($this->passwordHasher->hashPassword($user, $password));

        $this->entityManager->flush();
    }

    public function promoteUser(string $username, string $role)
    {
        /**
         * @var User $user
         */
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $username]);
        if (!$user) {
            throw new \Exception("Aucun utilisateur trouvé");
        }

        if (!$user->hasRole($role)) {
            $user->addRole($role);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            return true;
        }

        throw new \Exception("Cet utilisateur posséde déjà ce rôle");
    }

    public function demoteUser(string $username, string $role)
    {
        /**
         * @var User $user
         */
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $username]);
        if (!$user) {
            throw new \Exception("Aucun utilisateur trouvé");
        }

        if ($user->hasRole($role)) {
            $user->removeRole($role);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            return true;
        }

        throw new \Exception("Cet utilisateur ne posséde pas ce rôle");
    }
}
