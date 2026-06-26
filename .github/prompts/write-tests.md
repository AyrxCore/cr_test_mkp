# Écrire des Tests

## Contexte

- **Classe/Fonction** : [nom de la classe ou fonction à tester]
- **Comportement attendu** : [description]
- **Cas limites** : [cas d'erreur à couvrir]
- **Type** : [ ] Unitaire [ ] API [ ] Feature [ ] Integration [ ] Command

## Instructions

### Framework & structure

- **Pest PHP** avec syntaxe `describe()` / `it()` / `expect()`
- **Foundry** pour les factories (données de test)
- **Mockery** pour les mocks (tests unitaires)
- Fichier dans `tests/[Type]/` — le TestCase est auto-associé via `tests/Pest.php`

### Par type de test

#### Unitaire (`tests/Unit/`)

- Tester une classe/méthode isolée — mocker les dépendances avec Mockery
- Pas de BDD, pas de container Symfony
- Rapide, déterministe

```php
describe('MonService', function () {
    it('should do something', function () {
        $mock = Mockery::mock(Repository::class);
        $mock->shouldReceive('find')->andReturn($entity);
        $service = new MonService($mock);

        $result = $service->execute();

        expect($result)->toBeTrue();
    });
});
```

#### API (`tests/Api/`)

- Tester les endpoints HTTP — status codes, structure de réponse, sécurité
- Utiliser `$this->createClientWithCredentials()` pour l'authentification
- Vérifier les cas : 200/201 (succès), 401 (non authentifié), 403 (non autorisé), 404 (non trouvé), 422 (validation)

```php
describe('MonEntity API', function () {
    beforeEach(function () {
        $this->client = $this->createClientWithCredentials();
    });

    it('should list items', function () {
        MonEntityFactory::createMany(3);
        $response = $this->client->request('GET', '/api/mon-entities');

        expect($response->getStatusCode())->toBe(200);
        expect($response->toArray()['hydra:totalItems'])->toBe(3);
    });

    it('should require authentication', function () {
        $client = static::createClient();
        $response = $client->request('GET', '/api/mon-entities');

        expect($response->getStatusCode())->toBe(401);
    });
});
```

#### Feature (`tests/Feature/`)

- Tester un flux métier complet de bout en bout
- Utiliser les factories pour le setup, vérifier l'état final en BDD

#### Integration (`tests/Integration/`)

- Tester un service avec ses vraies dépendances (conteneur Symfony + BDD)
- `$this->getContainer()->get(MonService::class)`

#### Command (`tests/Command/`)

- Tester les commandes console via `CommandTester`
- Vérifier status code et output

### Conventions

- Pattern **Arrange / Act / Assert** dans chaque test
- `->group('NomDuGroupe')` pour le regroupement
- Helpers disponibles : `refreshEntity()`, `getFaker()`, `getResourcesMockData()`
- Noms de tests explicites : `it('should reject inactive adherent')`

## Exemple d'utilisation

```
Classe : App\Service\AdherentService (méthode activate)
Comportement attendu : active l'adhérent, met à jour updatedAt, flush en BDD
Cas limites : adhérent déjà actif, adhérent null
Type : [x] Unitaire  [x] Integration
```

## Validation

```bash
make all-tests-parallel
```
