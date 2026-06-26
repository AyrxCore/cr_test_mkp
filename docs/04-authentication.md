# 04 - Authentification et Sécurité

## 🔐 Vue d'ensemble

L'application utilise un système d'authentification **multi-couches** :

1. **JWT (JSON Web Tokens)** pour l'authentification de l'API interne
2. **OAuth2** pour communiquer avec l'API Uppler
3. **Auto-Login** pour les connexions depuis le système Neo

```
┌────────────────────────────────────────────────────────────┐
│                    AUTHENTIFICATION                         │
├────────────────────────────────────────────────────────────┤
│                                                             │
│  ┌─────────────┐     ┌─────────────┐     ┌──────────────┐  │
│  │  JWT Auth   │     │ Uppler OAuth│     │  Auto-Login  │  │
│  │  (Interne)  │     │  (Externe)  │     │    (Neo)     │  │
│  └──────┬──────┘     └──────┬──────┘     └──────┬───────┘  │
│         │                   │                   │          │
│         └───────────────────┴───────────────────┘          │
│                             │                               │
│                             ▼                               │
│                     ┌─────────────┐                         │
│                     │    User     │                         │
│                     │  + Account  │                         │
│                     └─────────────┘                         │
└────────────────────────────────────────────────────────────┘
```

## 🎫 JWT Authentication (Lexik JWT Bundle)

### Configuration

```yaml
# config/packages/security.yaml
security:
  firewalls:
    app:
      pattern: ^/api
      provider: app_user_provider
      user_checker: App\Security\UserChecker
      entry_point: jwt
      jwt: ~
      json_login:
        check_path: /api/authentication/token
        success_handler: lexik_jwt_authentication.handler.authentication_success
        failure_handler: lexik_jwt_authentication.handler.authentication_failure
```

### Flux de connexion standard

```
1. POST /api/authentication/token
   Body: { "username": "user", "password": "pass" }
         │
         ▼
2. Symfony Security valide les credentials
         │
         ▼
3. UserChecker vérifie que l'utilisateur est activé
         │
         ▼
4. JWT Token généré et retourné
   Response: { "token": "eyJ..." }
         │
         ▼
5. Token stocké en cookie HttpOnly (BEARER)
```

### UserChecker

Vérifie l'état de l'utilisateur avant authentification :

```php
// src/Security/UserChecker.php
class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }
        
        if (!$user->isEnabled()) {
            throw new DisabledException('Compte désactivé');
        }
    }
}
```

### Accès aux routes

```yaml
# config/packages/security.yaml
access_control:
  - { path: ^/api/authentication/token, roles: PUBLIC_ACCESS }
  - { path: ^/api/docs, roles: PUBLIC_ACCESS }
  - { path: ^/api/contact, roles: PUBLIC_ACCESS }
  - { path: ^/api/cms, roles: PUBLIC_ACCESS }
  - { path: ^/api/channels/by-host/, roles: PUBLIC_ACCESS }
  - { path: ^/api, roles: IS_AUTHENTICATED_FULLY }
  - { path: ^/, roles: PUBLIC_ACCESS }
```

## 🔄 OAuth2 avec Uppler

### Types de tokens

| Token | Usage | Stockage |
|-------|-------|----------|
| **Token Admin** | Actions en mode Operator (création comptes...) | Fichier `var/token.txt` |
| **Token User** | Actions scopées au buyer connecté | Session PHP |

### AbstractUpplerService

Classe de base pour tous les appels à l'API Uppler :

```php
// src/Service/AbstractUpplerService.php
abstract class AbstractUpplerService
{
    // Effectue une requête vers l'API Uppler
    public function request(
        string $method,
        string $path,
        array $options = [],
        bool $isAdmin = false,      // Utiliser le token Admin
        bool $withoutToken = false, // Sans token (endpoint public)
        bool $withCache = false,    // Activer le cache HTTP
    ): bool|ResponseInterface;
    
    // Obtient un token User et le stocke en session
    public function getUserToken(Account $account): void;
    
    // Obtient le token Admin (depuis fichier ou nouveau)
    private function getAdminToken(): string;
}
```

### Authentification d'un User vers Uppler

```php
// src/Service/UpplerAuthenticationService.php
class UpplerAuthenticationService extends AbstractUpplerService
{
    public function authenticateUser(Account $account): bool
    {
        // 1. Vider la session
        $session->clear();
        
        // 2. Obtenir un token OAuth pour ce compte
        $this->getUserToken($account);
        
        // 3. Vérifier que le token est en session
        if ($session->has('access_token')) {
            // Log de connexion
            $account->setLastConnexion(new DateTime('now'));
            return true;
        }
        
        return false;
    }
}
```

## 🔗 Auto-Login (depuis Neo)

Permet une connexion automatique via un lien signé.

### Principe

```
Neo génère un lien signé :
/login/neo-auto-login?email=xxx&timestamp=xxx&hash=xxx

Le hash est un SHA256(email + timestamp + adherent.hashkey) encodé en Base64
```

### Configuration

```yaml
# config/packages/security.yaml
firewalls:
  main:
    login_link:
      check_route: login_check
      lifetime: 300  # Validité du lien : 5 minutes
      signature_properties: ['id']
      success_handler: App\Security\Authentication\AutoLoginSuccessHandler
      failure_handler: App\Security\Authentication\AutoLoginFailureHandler
```

### Handlers

```php
// src/Security/Authentication/AutoLoginSuccessHandler.php
class AutoLoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    public function onAuthenticationSuccess(Request $request, TokenInterface $token): Response
    {
        // 1. Récupérer l'utilisateur
        // 2. Sélectionner le bon Account
        // 3. Authentifier vers Uppler
        // 4. Générer le JWT
        // 5. Stocker en cookie et rediriger vers /app
    }
}
```

## 🎭 Rôles et permissions

### Rôles disponibles

| Rôle | Description |
|------|-------------|
| `ROLE_USER` | Utilisateur standard (par défaut) |
| `ROLE_ADMIN` | Administrateur |
| `ROLE_SUPER_ADMIN` | Super administrateur |
| `ROLE_API` | Accès API (pour intégrations) |

### Hiérarchie

```yaml
role_hierarchy:
  ROLE_SUPER_ADMIN: [ROLE_SUPER_ADMIN]
  ROLE_ADMIN: ROLE_ADMIN
  ROLE_API: ROLE_API
```

### Voters (autorisations fines)

```php
// src/Security/Voter/AccountVoter.php
class AccountVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        return $subject instanceof Account;
    }
    
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        
        // L'utilisateur ne peut accéder qu'à ses propres comptes
        return $subject->getUser() === $user;
    }
}
```

## 🍪 Gestion des cookies

| Cookie | Contenu | HttpOnly | Secure |
|--------|---------|----------|--------|
| `BEARER` | JWT Token | ✅ | ✅ (prod) |
| `PHPSESSID` | Session PHP | ✅ | ✅ (prod) |
| `neoAutoLogin` | Flag auto-login | ❌ | ❌ |

## 🔒 Bonnes pratiques

1. **Toujours utiliser les services Uppler** pour les appels API externes
2. **Ne jamais exposer les credentials Uppler** côté frontend
3. **Vérifier les permissions** avec les Voters pour les ressources sensibles
4. **Logger les connexions** pour l'audit (LogAccountConnection)
5. **Utiliser `IS_AUTHENTICATED_FULLY`** pour les routes sensibles

