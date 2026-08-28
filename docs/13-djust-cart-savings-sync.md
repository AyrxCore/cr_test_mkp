# CartSavings & Sync Statuts Djust

**Ticket** : MKP-1449 | **Status** : ✅ En production

## Objectif

Créer `CartSavings` après paiement Djust et synchroniser les statuts de commande vers PowerBI deux fois par jour.

---

## Commandes

```bash
# Exécution manuelle du cron (sans passer par le Scheduler)
php bin/console app:sync-djust-orders-state -v

# Résultat attendu
[OK] Djust orders sync completed. processed=N updated=M skipped=K failed=0
```

**Résultat :**
- `processed` : nombre de CartSavings avec `orderId` traitées
- `updated` : nombre de statuts changés
- `skipped` : nombre sans changement
- `failed` : nombre d'erreurs API

---

## Behavior en Production

**La sync s'exécute automatiquement deux fois par jour, aux heures creuses (6h et 22h)** via Symfony Scheduler.

- Seulement si `APP_MODE=prod` (variable Docker)
- Exécutée par les workers Messenger (supervisord)
- Si une commande est active : changement de statut → logs `[INFO]`
- Si une commande n'est plus disponible : logs `[WARNING]`
- Erreur API : logs `[ERROR]` + compteur `failed` incrémenté

---

## Notes Techniques

**Pourquoi les credentials buyer pour le cron ?**

L'API Djust opérateur (`/v1/shop/commercial-orders/{id}`) ne supporte pas les orders créées via le flux paiement v2. Le cron utilise donc les credentials buyer de chaque account pour accéder à l'API v2.

**Format des IDs**
- `cartId` = référence panier Djust (ex: `260-211-8089583`)
- `orderId` = ID Djust paddé (ex: `0000625247`) 
- `orderState` = statut de l'`orderLogistics` correspondant au vendeur de la ligne (ex: `WAITING_SUPPLIER_APPROVAL`)
- `partnerId` = ID SUGAR/NEO du partenaire (vendeur) de l'`orderLogistic` (`supplierSnapshot.externalId`), utilisé pour le reporting PowerBI (MKP-1529). `null` si absent côté Djust.

**Sous le capot**

1. `supervisord` lance les workers `messenger:consume default scheduler_default`
2. Le transport `scheduler_default` vérifie si `DjustSchedule` doit déclencher
3. Deux fois par jour (6h et 22h) → `SyncDjustOrdersStateMessage` dispatched sur transport `default`
4. Un worker la consomme → `SyncDjustOrdersStateMessageHandler` → `DjustOrdersSyncService::sync()`


