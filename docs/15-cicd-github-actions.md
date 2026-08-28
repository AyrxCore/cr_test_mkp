---
version: '1.1'
updated: 2026-08
next-review: 2026-11
audience: équipe marketplace (dev + devops)
---

# CI/CD Marketplace — GitHub Actions

> Documentation des workflows de déploiement de la marketplace, migrés de **Jenkins** vers **GitHub Actions**.
> Articulation entre le repo `marketplace`, le repo `devops` (Terraform), AWS et GitHub Actions.

## Sommaire

- [Vue d'ensemble](#vue-densemble)
- [Les 4 acteurs](#les-4-acteurs)
- [Workflow `deploy.yml`](#workflow-deployyml)
- [Workflow `destroy.yml`](#workflow-destroyyml)
- [Configuration GitHub (Environments & secrets)](#configuration-github-environments--secrets)
- [Le mot de passe de la base](#le-mot-de-passe-de-la-base)
- [Runbook — opérations courantes](#runbook--opérations-courantes)
- [Correspondance Jenkins → GitHub Actions](#correspondance-jenkins--github-actions)
- [FAQ / Pièges connus](#faq--pièges-connus)

---

## Vue d'ensemble

Le déploiement repose sur **deux repos** :

- **`GroupeQantis/marketplace`** (ce repo) : code applicatif, `Dockerfile`, workflows GitHub Actions (`deploy.yml`, `destroy.yml`).
- **`GroupeQantis/devops`** : tout le code **Terraform** (modules + config par environnement) décrivant l'infra AWS.

Les workflows du repo `marketplace` **clonent `devops` à la volée** et exécutent Terraform depuis celui-ci. Aucun code Terraform ne vit dans `marketplace`.

```
┌────────────────────────┐        clone (DEVOPS_REPO_TOKEN)      ┌───────────────────────┐
│  GroupeQantis/          │  ───────────────────────────────►    │  GroupeQantis/devops  │
│  marketplace            │                                       │  (Terraform)          │
│                         │                                       │  marketplace/infra    │
│  .github/workflows/     │        terraform init/plan/apply      │  marketplace/app      │
│    deploy.yml           │  ◄─── exécuté sur ces dossiers ────   │  terraform_modules/   │
│    destroy.yml          │                                       └───────────────────────┘
│  Dockerfile             │
└───────────┬────────────┘
            │ build & push images (php, nginx)
            ▼
      ┌──────────────┐          apply Terraform          ┌──────────────────────────────┐
      │  AWS ECR     │  ────────────────────────────►    │  AWS (compte 211737105908)    │
      │  (registry)  │                                   │  région eu-west-3             │
      └──────────────┘                                   │  VPC / ECS / RDS Aurora / ALB │
                                                         │  Route53 / SSM Param Store    │
                                                         └──────────────────────────────┘
```

**Environnements** : `staging`, `preprod`, `prod` — isolés via des **Terraform workspaces** (state S3 partagé, une clé de state par workspace).

---

## Les 4 acteurs

| Acteur | Rôle |
| --- | --- |
| **GitHub Actions** (repo marketplace) | Orchestrateur. Déclenche build + Terraform, gère les secrets. |
| **Repo `devops`** | Source de vérité de l'infra : modules Terraform + `terraform.<env>.tfvars`. Cloné à chaque run. |
| **AWS** | Cible. ECR, ECS, RDS Aurora PostgreSQL, ALB, Route53, **SSM Parameter Store** (`DATABASE_URL_MARKETPLACE_*`). |
| **Terraform** (v1.9.8) | Exécuté par le workflow dans le repo `devops`. State S3, un **workspace par environnement**. |

Découpage Terraform dans `devops` :

- `marketplace/infra` → VPC, cluster ECS, **RDS Aurora**, ALB, Route53, EC2 tunnel.
- `marketplace/app` → task definitions ECS, service, secrets injectés, commandes post-déploiement.

---

## Workflow `deploy.yml`

**Déclenchement** : manuel (`workflow_dispatch`) depuis l'onglet **Actions**.

| Input | Description | Défaut |
| --- | --- | --- |
| `environment` | `staging` / `preprod` / `prod` | — (obligatoire) |
| `git_ref` | Branche, tag ou SHA à builder et déployer (app) | `main` |
| `deploy_infra` | Forcer le (re)provisionnement infra (VPC/ECS/RDS) avant l'app. Auto-activé en **staging/preprod** si l'infra de l'env est absente (self-heal, ex. après un destroy). | `false` |

### Enchaînement des jobs

```
        detect-infra   (needs_infra = deploy_infra==true OU state infra vide, prod exclue du self-heal)
              │
      needs_infra ?
        │          │
        ▼          │
   infra-plan      │ (skippé si infra déjà présente et non forcée)
        │          │
        ▼          │
   infra-apply ◄───┘   [environment: <env>]
        │
        ▼
   build-and-push        (toujours : build images php + nginx → ECR)
        │
        ▼
   app-plan              (terraform plan sur marketplace/app)
        │
        ▼
   app-apply ◄────────── [environment: <env>]
```

| Job | Ce qu'il fait |
| --- | --- |
| `detect-infra` | Décide s'il faut provisionner l'infra : `true` si `deploy_infra=true` **ou** (hors prod) si le state Terraform de l'env est vide. Publie `needs_infra`. |
| `infra-plan` / `infra-apply` | `terraform plan`/`apply` sur `marketplace/infra`. Le plan est publié dans le job summary. |
| `build-and-push` | Build des images `marketplace-php`/`marketplace-nginx`, push vers ECR. |
| `app-plan` / `app-apply` | `terraform plan`/`apply` sur `marketplace/app` (tag d'image buildé) — déploie ECS et joue les commandes post-deploy. |

**Points clés :**

- **Self-healing infra limité à staging/preprod.** La prod n'a pas de chemin de destroy (voir plus bas) : son infra ne doit jamais être légitimement vide. Pour éviter qu'une erreur transitoire (lock d'état, backend S3) soit prise à tort pour « infra absente » et déclenche un apply non voulu, `detect-infra` force `needs_infra=false` pour `environment=prod` sauf si `deploy_infra=true` est coché explicitement.
- `build-and-push` et les jobs `app-*` s'exécutent **toujours**, même quand l'infra est skippée (conditions `if:` explicites, voir [FAQ](#faq--pièges-connus)).
- Les jobs **`*-apply`** (et `infra-plan`, pour accéder à `DB_PASSWORD` au moment du plan) portent `environment: <env>`. **⚠️ À ce jour, aucun required reviewer n'est configuré sur l'environnement `prod`** — l'apply s'exécute donc immédiatement, sans approbation manuelle. À configurer dans *Settings → Environments → prod* si un gate humain est souhaité avant un apply prod.
- Terraform est **pinné en 1.9.8**, `terraform init --upgrade` (providers `~> 5.0`, pas de lock file committé → dernier 5.x).

---

## Workflow `destroy.yml`

Remplace les jobs Jenkins `destroy-app` / `destroy-infra`.

**Déclenchement :**

- **Manuel** : `environment` (`staging`/`preprod` uniquement), `destroy_infra` (bool), `confirm` (bool, garde-fou).
- **Automatique** : cron `0 0 * * *` (chaque nuit) → détruit **staging** (app + infra).

**Sécurité :**

- `prod` **n'est pas** une option (ni manuelle, ni planifiée) — aucun chemin de destruction pour la prod, par conception.
- Un job `Safety guard` échoue explicitement si jamais `environment=prod`.
- La destruction manuelle exige `confirm=true`.

**Enchaînement :** `resolve-context` → `destroy-app` → `destroy-infra` (si `destroy_infra=true`). Chaque job vérifie que le workspace existe avant de détruire (idempotent).

> ⚠️ Pas de redéploiement automatique après le destroy nocturne : relancer `deploy.yml` manuellement le matin. L'infra staging est reprovisionnée automatiquement (self-heal), pas besoin de cocher `deploy_infra`.

---

## Configuration GitHub (Environments & secrets)

### Environments (Settings → Environments)

`staging`, `preprod`, `prod`. Aucun n'a de required reviewers configuré actuellement — à faire pour `prod` si un gate d'approbation est souhaité.

### Secrets **repo-level** (Settings → Secrets → Actions)

Utilisés par les jobs sans contexte `environment:` (`detect-infra`, `build-and-push`, `app-plan`) :

| Secret | Usage |
| --- | --- |
| `AWS_ACCESS_KEY_ID` / `AWS_SECRET_ACCESS_KEY` | Clé IAM `github-actions` (ECR + Terraform). |
| `DEVOPS_REPO_TOKEN` | PAT fine-grained, lecture `Contents` sur `GroupeQantis/devops` (clone du repo). |

### Secrets **par environnement**

| Secret | Usage |
| --- | --- |
| `DB_PASSWORD` | Passé en `TF_VAR_db_password` → devient le `master_password` du cluster Aurora, **appliqué à la création** (y compris lors d'un restore de snapshot). Doit être identique au mot de passe dans `DATABASE_URL_MARKETPLACE_<ENV>`. |

> **Pourquoi les secrets AWS/DEVOPS sont repo-level et pas par environnement ?** Un job ne peut lire les secrets d'un environnement que s'il déclare `environment:`. `AWS_ACCESS_KEY_ID`/`AWS_SECRET_ACCESS_KEY`/`DEVOPS_REPO_TOKEN` restent donc repo-level pour être lisibles par tous les jobs ; `DB_PASSWORD` est spécifique à l'environnement.
>
> ⚠️ **Conséquence (bug corrigé)** : `infra-plan` doit déclarer `environment: ${{ inputs.environment }}`, sinon `DB_PASSWORD` y vaut chaîne vide au moment du plan — voir section suivante.

---

## Le mot de passe de la base

Le module RDS (`devops/terraform_modules/rds_serverless_v2`) crée le cluster Aurora avec :

```hcl
master_username     = "root"
master_password     = var.db_password
snapshot_identifier = data.aws_db_cluster_snapshot.rds_snapshot_identifier.id
```

Le snapshot source (`marketplace/infra/variables.tf`) est **toujours celui de la prod**, pour tous les environnements.

**Mécanisme :** même en restaurant depuis un snapshot, le provider AWS applique `master_password` juste après le restore (`ModifyDBCluster`), dans la même opération de `create` (Terraform affiche « added », jamais « changed »). **Condition : le `db_password` doit être non vide au moment du `plan`** — l'`apply` rejoue le plan sauvegardé en artifact, pas la valeur au moment de l'apply. D'où l'obligation pour `infra-plan` de déclarer `environment:`.

**Chaque environnement a donc son propre mot de passe, appliqué uniquement à la création du cluster.**

### ⚠️ Piège majeur : Terraform ne détecte pas un changement de mot de passe sur un cluster existant

L'API AWS ne renvoie jamais `master_password` (write-only). Terraform compare sa **config** à son **state**, jamais à la valeur réelle sur le cluster. Donc **changer `DB_PASSWORD` puis relancer un deploy sur un cluster existant ne change rien** (`0 added, 0 changed, 0 destroyed`).

| Environnement | Cycle de vie | Effet d'un changement de `DB_PASSWORD` |
| --- | --- | --- |
| **staging** | Détruit + recréé chaque nuit | Appliqué automatiquement à la prochaine recréation. |
| **preprod** | Long-lived | Pas appliqué par un simple `deploy`. Il faut `destroy` puis `deploy`, ou un `modify-db-cluster` manuel. |
| **prod** | Long-lived, jamais restauré | Ne pas toucher hors procédure DBA. |

**Changer le mot de passe d'un cluster existant sans le recréer** (~1 min) :

```bash
# 1) récupérer le mot de passe cible depuis DATABASE_URL (SSM Parameter Store)
PW=$(aws ssm get-parameter --name DATABASE_URL_MARKETPLACE_<ENV> --with-decryption \
      --region eu-west-3 --query "Parameter.Value" --output text \
      | sed -E 's#^[^:]+://[^:]+:([^@]*)@.*#\1#')
# 2) appliquer au cluster
aws rds modify-db-cluster --db-cluster-identifier marketplace-<env>-postgre-cluster \
      --master-user-password "$PW" --apply-immediately --region eu-west-3
# 3) attendre le retour à "available" puis forcer un redéploiement ECS
aws ecs update-service --cluster marketplace-<env> --service app \
      --force-new-deployment --region eu-west-3
unset PW
```

> `DATABASE_URL_MARKETPLACE_*` est stocké dans **SSM Parameter Store** (pas Secrets Manager). Les tâches ECS y accèdent via `valueFrom`.

### Ce qui sert réellement à se connecter

L'app ne se connecte jamais avec `DB_PASSWORD` directement : elle lit **un seul paramètre SSM**, injecté dans la task definition ECS — `DATABASE_URL_MARKETPLACE_STG` / `_PPR` / `_PRD` selon l'env (`postgresql://root:<mot_de_passe>@<host>:5432/<db>?...`). Ce même secret est réutilisé par les tâches post-deploy (SQL et PHP).

### La règle d'or

> Pour chaque environnement, `DB_PASSWORD` (secret GitHub) et le mot de passe dans `DATABASE_URL_MARKETPLACE_<ENV>` (SSM) **doivent être strictement identiques** — ils peuvent différer d'un environnement à l'autre. Sur un cluster déjà existant, changer `DB_PASSWORD` seul ne suffit pas (voir piège majeur ci-dessus).

**Symptôme d'un mauvais alignement :**

```
SQLSTATE[08006] [7] connection to server at "postgresql.marketplace-staging.local"
port 5432 failed: FATAL: password authentication failed for user "root"
```

**Fix** : aligner `DB_PASSWORD` et `DATABASE_URL_MARKETPLACE_<ENV>`, puis pousser via `aws rds modify-db-cluster` (ci-dessus) et forcer un redéploiement ECS.

> 💡 Comportement identique à Jenkins : `MARKETPLACE_DB_PASSWORD_STG` et `DB_PASSWORD` jouaient exactement le même rôle.

---

## Runbook — opérations courantes

### Déployer l'app (cas courant, sans toucher l'infra)

1. Actions → **Deploy** → *Run workflow*.
2. `environment` = cible, `git_ref` = branche/tag/SHA, `deploy_infra` = **false**.
3. Suivre `detect-infra` → `build-and-push` → `app-plan` → `app-apply`. Si l'infra est déjà présente, `infra-plan`/`infra-apply` sont skippés.

### Redéployer après un destroy (staging/preprod)

Laisser `deploy_infra=false` suffit : `detect-infra` voit le state vide et reprovisionne l'infra automatiquement avant l'app. (Ne s'applique pas à `prod`, qui n'a pas de self-heal — voir plus haut.)

### Déployer avec (re)provisionnement infra forcé

`deploy_infra=true`. Si le cluster n'existe pas, il est recréé depuis le snapshot prod avec `master_password = DB_PASSWORD`. S'il existe déjà, le mot de passe n'est **pas** retouché (voir [piège majeur](#piège-majeur--terraform-ne-détecte-pas-un-changement-de-mot-de-passe-sur-un-cluster-existant)).

### Déployer en prod

`environment=prod`. Testé et validé en app-only (`deploy_infra=false`) : build → `app-plan` → `app-apply`, aucune approbation manuelle actuellement (pas de reviewers configurés). Le post-deploy prod est un **NOOP** par défaut (pas de resync Djust ni de script SQL, contrairement à preprod/staging).

### Détruire un environnement (staging/preprod)

Actions → **Destroy** → `environment`, `destroy_infra`, `confirm=true`. Staging est de toute façon détruit automatiquement chaque nuit.

### Rotation d'un mot de passe de base

1. Mettre à jour le secret d'environnement GitHub `DB_PASSWORD`.
2. Mettre à jour `DATABASE_URL_MARKETPLACE_<ENV>` dans SSM avec la même valeur.
3. Appliquer au cluster existant : **staging** → attendre la recréation nocturne ou `modify-db-cluster` immédiat ; **preprod** → `destroy` puis `deploy`, ou `modify-db-cluster` ; **prod** → procédure DBA habituelle.
4. Forcer un redéploiement ECS (`--force-new-deployment`).

> 💡 Préférer un mot de passe purement alphanumérique (évite les soucis d'encodage dans l'URL et dans la tâche post-deploy `psql`).

---

## Correspondance Jenkins → GitHub Actions

| Jenkins | GitHub Actions |
| --- | --- |
| `deploy-infra` | `deploy.yml` jobs `infra-plan` + `infra-apply` |
| `deploy-app` | `deploy.yml` jobs `build-and-push` + `app-plan` + `app-apply` |
| `destroy-app` | `destroy.yml` job `destroy-app` |
| `destroy-infra` | `destroy.yml` job `destroy-infra` |
| Crons `destroy-app @ H0` / `destroy-infra @ H1` | `destroy.yml` `schedule: 0 0 * * *` (staging) |
| Credentials Jenkins (`AWS_TERRAFORM`, `MARKETPLACE_DB_PASSWORD_*`) | Secrets GitHub (repo + environnement) |

Comportement fonctionnel équivalent : mêmes commandes Terraform, même state S3, mêmes ressources AWS.

---

## FAQ / Pièges connus

**Le workflow n'apparaît pas dans l'onglet Actions.**
`workflow_dispatch` n'est déclenchable que s'il existe sur la branche par défaut du dépôt. Merger le fichier sur cette branche d'abord.

**`app-plan` / `app-apply` sont « skipped ».**
Un ancêtre `skipped` (`infra-apply` quand `deploy_infra=false`) se propage via le `success()` implicite. Résolu par des `if: always() && needs.<dep>.result == 'success'` explicites.

**`ecr:GetAuthorizationToken ... not authorized`.**
La policy IAM de `github-actions` doit inclure les permissions ECR (login + push).

**`403 Write access to repository not granted` au checkout de `devops`.**
`DEVOPS_REPO_TOKEN` doit être un fine-grained token avec `Contents: read` sur `GroupeQantis/devops`.

**`password authentication failed for user "root"` après un deploy.**
Voir [Le mot de passe de la base](#le-mot-de-passe-de-la-base) : `DB_PASSWORD` et `DATABASE_URL_MARKETPLACE_<ENV>` doivent être identiques.
