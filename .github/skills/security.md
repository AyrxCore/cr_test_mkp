# Skill: Sécuriser l'Application

## Identité

Tu es un expert en sécurité applicative web. Tu identifies les vulnérabilités, appliques les bonnes pratiques OWASP et garantis la sécurité des données sensibles dans une application Symfony + Vue.js.

## OWASP Top 10 - Application au Projet

### 1. Injection (SQL, LDAP, OS)

```php
// ❌ Mauvais - SQL brut avec concaténation
$sql = "SELECT * FROM adherent WHERE email = '" . $email . "'";
$result = $connection->executeQuery($sql);

// ✅ Bon - Doctrine QueryBuilder avec paramètres
$qb = $this->createQueryBuilder('a')
    ->where('a.email = :email')
    ->setParameter('email', $email)
    ->getQuery();

// ✅ Bon - Requête native paramétrée
$sql = 'SELECT * FROM adherent WHERE email = :email';
$result = $connection->executeQuery($sql, ['email' => $email]);
```

### 2. Cross-Site Scripting (XSS)

```php
// ❌ Mauvais - Twig sans échappement
{{ adherent.nom|raw }}

// ✅ Bon - Échappement automatique Twig (par défaut)
{{ adherent.nom }}
```

```typescript
// ❌ Mauvais - Injection HTML en Vue.js
<div v-html="userInput"></div>

// ✅ Bon - Interpolation sécurisée (auto-échappée)
<div>{{ userInput }}</div>

// ✅ Si v-html est nécessaire, sanitiser d'abord
import DOMPurify from 'dompurify';
const sanitized = DOMPurify.sanitize(userInput);
```

### 3. Cross-Site Request Forgery (CSRF)

```php
// ✅ Symfony gère le CSRF automatiquement pour les formulaires
// Vérifier que le token est validé dans les Processors personnalisés

use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

final readonly class SensitiveProcessor implements ProcessorInterface
{
    public function __construct(
        private CsrfTokenManagerInterface $csrfTokenManager,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        // Pour les endpoints API : utiliser JWT (stateless, pas de CSRF nécessaire)
        // Pour les formulaires Twig : vérifier le token CSRF
        return $data;
    }
}
```

### 4. Broken Authentication

```php
// ✅ Utiliser le système de sécurité Symfony
// config/packages/security.yaml
security:
    password_hashers:
        App\Entity\User:
            algorithm: auto  # bcrypt ou argon2i automatique

    firewalls:
        api:
            stateless: true
            jwt: ~  # LexikJWTAuthenticationBundle
```

```php
// ✅ Validation de mot de passe robuste
use Symfony\Component\Validator\Constraints as Assert;

#[Assert\PasswordStrength(minScore: PasswordStrength::STRENGTH_MEDIUM)]
#[Assert\NotCompromisedPassword]
private string $plainPassword;
```

### 5. Broken Access Control

```php
// ❌ Mauvais - Pas de contrôle d'accès
#[ApiResource]
class Adherent {}

// ✅ Bon - Voters pour contrôle d'accès granulaire
#[ApiResource(
    operations: [
        new Get(security: "is_granted('ADHERENT_VIEW', object)"),
        new Put(security: "is_granted('ADHERENT_EDIT', object)"),
        new Delete(security: "is_granted('ADHERENT_DELETE', object)"),
    ],
)]
class Adherent {}
```

```php
// ✅ Voter avec logique d'accès métier
final class AdherentVoter extends Voter
{
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        return match ($attribute) {
            'ADHERENT_VIEW' => $this->canView($subject, $user),
            'ADHERENT_EDIT' => $this->canEdit($subject, $user),
            'ADHERENT_DELETE' => $user->hasRole('ROLE_ADMIN'),
            default => false,
        };
    }
}
```

### 6. Security Misconfiguration

```yaml
# ✅ Headers de sécurité (config Nginx ou Symfony)
# Nelmio Security Bundle
nelmio_security:
    content_type:
        nosniff: true
    xss_protection:
        enabled: true
    clickjacking:
        paths:
            '^/.*': DENY
    csp:
        enabled: true
```

## Sécurisation des Endpoints Sensibles

### Paiement

```php
<?php

declare(strict_types=1);

namespace App\Service\Payment;

use Psr\Log\LoggerInterface;
use Symfony\Component\Lock\LockFactory;

final readonly class SecurePaymentService
{
    public function __construct(
        private PaymentGatewayInterface $gateway,
        private LockFactory $lockFactory,
        private LoggerInterface $logger,
    ) {
    }

    public function processPayment(Payment $payment): PaymentResult
    {
        // 1. Lock pour éviter le double traitement
        $lock = $this->lockFactory->createLock(
            sprintf('payment_%s', $payment->getId()),
            ttl: 30.0,
        );

        if (!$lock->acquire()) {
            throw new PaymentAlreadyProcessingException();
        }

        try {
            // 2. Valider l'intégrité
            $this->validatePaymentIntegrity($payment);

            // 3. Traiter
            $result = $this->gateway->charge($payment);

            // 4. Logger sans données sensibles
            $this->logger->info('Payment processed', [
                'paymentId' => $payment->getId(),
                'status' => $result->getStatus(),
                // ❌ JAMAIS : 'cardNumber' => $payment->getCardNumber(),
            ]);

            return $result;
        } finally {
            $lock->release();
        }
    }
}
```

### Gestion de Compte

```php
// ✅ Rate limiting sur les endpoints sensibles
#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/auth/login',
            // Rate limit : 5 tentatives par minute
        ),
        new Post(
            uriTemplate: '/auth/reset-password',
            // Rate limit : 3 tentatives par heure
        ),
    ],
)]
```

## Validation des Entrées

```php
// ✅ TOUJOURS valider à chaque couche

// Couche DTO (entrée API)
final readonly class AdherentCreateInput
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(min: 2, max: 255)]
        public string $nom,

        #[Assert\NotBlank]
        #[Assert\Email]
        public string $email,

        #[Assert\Regex(pattern: '/^[0-9]{10}$/', message: 'Numéro invalide')]
        public ?string $telephone = null,
    ) {
    }
}

// Couche Entity (invariants métier)
#[ORM\Entity]
class Adherent
{
    public function setEmail(string $email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Email invalide');
        }
        $this->email = $email;
    }
}
```

## Sécurité Vue.js

```typescript
// ✅ Stocker le JWT de manière sécurisée
// Préférer httpOnly cookie côté serveur
// Si localStorage nécessaire, attention au XSS

// ✅ Nettoyer les données avant affichage
// Vue.js échappe automatiquement avec {{ }}
// Ne JAMAIS utiliser v-html avec des données utilisateur

// ✅ Valider les entrées côté client aussi
const validateEmail = (email: string): boolean => {
  const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return regex.test(email);
};

// ✅ Protéger les routes sensibles
router.beforeEach((to, from, next) => {
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next({ name: 'login' });
    return;
  }
  if (to.meta.requiredRole && !authStore.hasRole(to.meta.requiredRole)) {
    next({ name: 'forbidden' });
    return;
  }
  next();
});
```

## Gestion des Secrets

```bash
# ❌ JAMAIS dans le code source
DATABASE_URL="postgresql://user:password@localhost/db"
STRIPE_SECRET_KEY="sk_live_xxxx"

# ✅ Utiliser les variables d'environnement
# .env (valeurs par défaut, committé)
DATABASE_URL="postgresql://app:!ChangeMe!@127.0.0.1:5432/app?serverVersion=15"

# .env.local (valeurs réelles, NON committé, dans .gitignore)
DATABASE_URL="postgresql://real_user:real_password@prod-host:5432/prod_db"
```

```php
// ✅ Symfony Secrets pour la production
// php bin/console secrets:set DATABASE_URL
// Les secrets sont chiffrés et versionnés en toute sécurité
```

## Anti-Patterns de Sécurité

| ❌ Anti-Pattern | ✅ Bonne Pratique |
|----------------|-------------------|
| SQL brut concaténé | QueryBuilder avec paramètres |
| `v-html` avec données utilisateur | Interpolation `{{ }}` |
| Mot de passe en clair dans les logs | Logger uniquement les IDs |
| Pas de contrôle d'accès | Voters sur chaque opération |
| Secrets en dur dans le code | Variables d'environnement / Symfony Secrets |
| Pas de rate limiting | Limiter les tentatives (login, reset password) |
| Pas de validation | Valider à chaque couche (DTO, Entity, Service) |
| Permissions trop larges | Principe du moindre privilège |

## Checklist Sécurité

Avant chaque mise en production :

- [ ] Pas d'injection SQL (requêtes paramétrées uniquement)
- [ ] Pas de XSS (pas de `v-html` avec données utilisateur, pas de `|raw` en Twig)
- [ ] CSRF protégé (JWT stateless ou tokens CSRF)
- [ ] Contrôle d'accès sur chaque endpoint (Voters)
- [ ] Validation des entrées à chaque couche
- [ ] Pas de données sensibles dans les logs
- [ ] Pas de secrets en dur dans le code source
- [ ] Mots de passe hashés (bcrypt/argon2)
- [ ] Rate limiting sur les endpoints sensibles
- [ ] Headers de sécurité configurés (CSP, X-Frame-Options, etc.)
- [ ] Locks sur les opérations concurrentes critiques (paiement)
- [ ] Dépendances à jour (`composer audit`, `npm audit`)

## Instructions

1. **TOUJOURS** utiliser des requêtes paramétrées (jamais de concaténation SQL)
2. **TOUJOURS** valider les entrées à chaque couche (DTO, Entity, Service)
3. **TOUJOURS** utiliser les Voters pour le contrôle d'accès
4. **JAMAIS** de données sensibles dans les logs ou messages d'erreur
5. **JAMAIS** de secrets en dur dans le code source
6. **JAMAIS** de `v-html` avec des données utilisateur non sanitisées
7. Appliquer le principe du moindre privilège
8. Vérifier les dépendances régulièrement (`composer audit`, `npm audit`)

