# 04 - Authentification et Sécurité

## 🔐 Vue d'ensemble

L'application utilise un système d'authentification **multi-couches** :

1. **JWT (JSON Web Tokens)** pour l'authentification de l'API interne
2. **OAuth2** pour communiquer avec l'API Djust
3. **Auto-Login** pour les connexions depuis le système Neo

```
┌────────────────────────────────────────────────────────────┐
│                    AUTHENTIFICATION                         │
├────────────────────────────────────────────────────────────┤
│                                                             │
│  ┌─────────────┐     ┌─────────────┐     ┌──────────────┐  │
│  │  JWT Auth   │     │ Djust OAuth │     │  Auto-Login  │  │
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

## 🔄 OAuth2 avec Djust

### Types de tokens

| Token | Usage | Stockage |
|-------|-------|----------|
| **Token Operator** | Actions en mode admin (sync, gestion globale) | Cache Symfony |
| **Token Buyer** | Actions scopées au buyer connecté | Session PHP |

### DjustHttpClientService

Service central pour tous les appels à l'API Djust :

```php
// src/Service/Djust/DjustHttpClientService.php
class DjustHttpClientService
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly CacheInterface $cache,
        #[Autowire(env: 'DJUST_API_BASE_URL')]
        private readonly string $baseUrl,
        #[Autowire(env: 'DJUST_API_USERNAME')]
        private readonly string $username,
        #[Autowire(env: 'DJUST_API_PASSWORD')]
        private readonly string $password,
    ) {}
    
    // Effectue une requête vers l'API Djust
    public function get(string $endpoint, array $params = [], bool $isOperator = false): array;
    public function post(string $endpoint, array $data = [], bool $isOperator = false): array;
    public function put(string $endpoint, array $data = [], bool $isOperator = false): array;
    public function delete(string $endpoint, bool $isOperator = false): array;
    
    // Obtient un token ACCOUNT valide (depuis session ou nouveau)
    public function getValidAccountToken(): string;
    
    // Obtient le token OPERATOR (depuis cache ou nouveau)
    private function getOperatorToken(): string;
}
```

### Authentification d'un User vers Djust

```php
// src/Service/Djust/DjustAuthenticationService.php
class DjustAuthenticationService
{
    public function authenticateUser(Account $account, bool $isConnectionLogged = true): bool
    {
        // 1. Vérifier les credentials
        if (empty($account->getDjustUsername()) || empty($account->getDjustPassword())) {
            return false;
        }
        
        // 2. Obtenir un token OAuth pour ce compte
        $accessToken = $this->djustHttpClientService->getValidAccountToken();
        
        // 3. Stocker le token en session
        $session->set('access_token', (object) ['access_token' => $accessToken]);
        
        // 4. Logger la connexion
        if ($isConnectionLogged) {
            $this->logAccountConnectionService->createLog($account);
        }
        
        return true;
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
        // 3. Authentifier vers Djust
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

1. **Toujours utiliser `DjustHttpClientService`** pour les appels API externes
2. **Ne jamais exposer les credentials Djust** côté frontend
3. **Vérifier les permissions** avec les Voters pour les ressources sensibles
4. **Logger les connexions** pour l'audit (LogAccountConnection)
5. **Utiliser `IS_AUTHENTICATED_FULLY`** pour les routes sensibles

