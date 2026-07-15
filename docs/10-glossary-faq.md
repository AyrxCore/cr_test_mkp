# 10 - Glossaire & FAQ

## 📖 Glossaire

### Termes métier

| Terme | Définition |
|-------|------------|
| **Adherent** | Entreprise adhérente à un channel, provient du système Neo/Sugar |
| **Accord** | Accord commercial entre un adhérent et un partenaire fournisseur |
| **AccordStatut** | État de souscription d'un adhérent à un accord |
| **Account** | Compte utilisateur lié à Djust (credentials OAuth) |
| **Buyer** | Acheteur sur la marketplace (utilisateur final) |
| **Channel** | Canal/tenant de l'application (multi-marque) |
| **Partner** | Partenaire fournisseur référencé sur la marketplace |
| **PartnerStore** | Magasin physique d'un partenaire |
| **Seller** | Vendeur sur Djust |
| **WhiteLabel** | Mode marque blanche (sans logo Qantis) |

### Termes techniques

| Terme | Définition |
|-------|------------|
| **API Platform** | Framework PHP pour créer des APIs REST |
| **Composition API** | Pattern Vue.js 3 avec `<script setup>` |
| **DTO** | Data Transfer Object - objet de transfert de données |
| **JWT** | JSON Web Token - token d'authentification |
| **Pinia** | State management pour Vue.js 3 |
| **State Provider** | API Platform : fournit les données pour une opération |
| **State Processor** | API Platform : traite les données en entrée |
| **Djust** | Plateforme SaaS de marketplace B2B |
| **Voter** | Symfony : contrôle d'accès fin sur les ressources |

### Identifiants Djust

| ID | Description |
|----|-------------|
| `djustCustomerAccountId` | ID du customer account dans Djust |
| `djustCustomerUserId` | ID du customer user dans Djust |
| `djustSellerId` | ID du vendeur (seller) dans Djust |

---

## ❓ FAQ

### Développement

**Q: Comment ajouter une nouvelle route API ?**

R: Utiliser les attributs API Platform sur l'entité ou créer un controller custom :

```php
// Méthode 1 : API Platform
#[ApiResource(operations: [new Get(), new Post()])]
class MyEntity {}

// Méthode 2 : Controller custom
#[Route('/api/my-endpoint')]
class MyController extends AbstractController {}
```

---

**Q: Comment ajouter un nouveau store Pinia ?**

R: Créer un fichier dans `assets/vuejs/stores/` :

```typescript
// stores/myStore.ts
import { defineStore } from 'pinia'

export const useMyStore = defineStore('myStore', {
  state: () => ({ /* ... */ }),
  actions: { /* ... */ },
  getters: { /* ... */ },
})
```

---

**Q: Comment appeler l'API Djust ?**

R: Toujours utiliser `DjustHttpClientService` :

```php
class MyDjustService
{
    public function __construct(
        private readonly DjustHttpClientService $djustHttpClient
    ) {}

    public function myMethod(): array
    {
        return $this->djustHttpClient->get('v2/shop/endpoint');
    }
}
```

---

**Q: Comment tester un endpoint API ?**

R: Utiliser la classe `ApiTestCase` :

```php
it('should work', function () {
    $this->authenticateAs($user);
    $this->client->request('GET', '/api/my-endpoint');
    expect($this->client->getResponse()->getStatusCode())->toBe(200);
});
```

---

### Authentification

**Q: Comment fonctionne l'auto-login depuis Neo ?**

R: Neo génère un lien signé avec un hash SHA256 basé sur l'email, le timestamp et la hashkey de l'adhérent. Le `AutoLoginSuccessHandler` valide ce hash et authentifie l'utilisateur.

---

**Q: Où est stocké le token JWT ?**

R: Dans un cookie HttpOnly nommé `BEARER`. Il n'est pas accessible en JavaScript pour des raisons de sécurité.

---

**Q: Où est stocké le token Djust ?**

R: 
- **Token Buyer** : En session PHP (clés `djust_account_access_token`, `djust_account_refresh_token`, `djust_account_expires_at`)
- **Token Operator** : Dans le cache Symfony (clé `djust_operator_token`)

---

### Base de données

**Q: Comment créer une nouvelle migration ?**

```bash
make exec php bin/console doctrine:migrations:diff
make database-migrations
```

---

**Q: Comment réinitialiser la base de données de test ?**

```bash
make init-tests
```

---

### Multi-tenant

**Q: Comment le channel est-il détecté ?**

R: 
1. Le frontend fait `GET /api/channels/by-host/{hostname}`
2. Le backend retourne le channel correspondant
3. Toutes les requêtes suivantes incluent `X-channel: CODE` dans les headers

---

**Q: Comment ajouter un nouveau channel ?**

R: Ajouter la configuration dans `config/channels.yaml` et exécuter les migrations/seeds.

---

### Déploiement

**Q: Comment déployer en production ?**

R: Le projet utilise un pipeline Jenkins (voir `Jenkinsfile`).

---

**Q: Comment vérifier la qualité du code avant commit ?**

```bash
make lint        # Tous les linters
make phpstan     # Analyse statique PHP
make all-tests   # Tous les tests
```

---

## 🔗 Liens utiles

### Documentation officielle

- [Symfony 6.4](https://symfony.com/doc/6.4/index.html)
- [API Platform](https://api-platform.com/docs/)
- [Vue.js 3](https://vuejs.org/guide/introduction.html)
- [Pinia](https://pinia.vuejs.org/)
- [Tailwind CSS](https://tailwindcss.com/docs)
- [Pest PHP](https://pestphp.com/docs/writing-tests)
- [Doctrine ORM](https://www.doctrine-project.org/projects/orm.html)

### Outils

- [Postman](https://www.postman.com/) - Tester les APIs
- [TablePlus](https://tableplus.com/) - Client PostgreSQL
- [Vue DevTools](https://devtools.vuejs.org/) - Extension navigateur

---

## 📞 Contacts & Support

Pour toute question sur le projet :

1. Consulter d'abord cette documentation
2. Vérifier les issues existantes sur le repo
3. Contacter l'équipe technique

---
