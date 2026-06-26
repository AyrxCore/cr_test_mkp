# HTTPS en local

Ce projet tourne en **HTTPS uniquement** (port `8087` → `443` dans le container nginx).  
Les certificats SSL ne sont **pas versionnés** dans le repo : chaque développeur doit les générer une fois sur sa machine.

---

## Prérequis

### 1. Installer `mkcert`

`mkcert` génère des certificats SSL locaux reconnus nativement par le navigateur (pas d'avertissement de sécurité).

**macOS**
```bash
brew install mkcert
```

**Linux (Debian/Ubuntu)**
```bash
sudo apt install libnss3-tools
VERSION=$(curl -s https://api.github.com/repos/FiloSottile/mkcert/releases/latest | grep '"tag_name"' | cut -d'"' -f4)
curl -Lo mkcert "https://github.com/FiloSottile/mkcert/releases/download/${VERSION}/mkcert-${VERSION}-linux-amd64"
chmod +x mkcert && sudo mv mkcert /usr/local/bin/
```

> 📖 Doc officielle : https://github.com/FiloSottile/mkcert

---

## Installation (une seule fois par machine)

### 2. Générer les certificats

```bash
make generate-certs
```

Cette commande :
- installe la CA locale de `mkcert` dans le trousseau système (`mkcert -install`)
- génère `docker/nginx/certs/localhost.pem` et `docker/nginx/certs/localhost-key.pem`
- ces fichiers sont dans `.gitignore` → ils restent uniquement en local

### 3. Ajouter le domaine dans `/etc/hosts`

Pour accéder au projet via `https://marketplace.qantis.local:8087`, ajouter la ligne suivante dans `/etc/hosts` :

```bash
sudo sh -c 'echo "127.0.0.1 marketplace.qantis.local" >> /etc/hosts'
```

> ⚠️ Cette modification est locale à votre machine et ne touche pas au repo.
> Elle ne doit être faite que si le projet n'est pas déjà en place sur la machine. Vérifier pour éviter les doublons.

---

## Lancement

Si projet déjà lancé, d'abord `docker stop` puis `docker compose up --build`

```bash
make up
```

L'application est ensuite accessible sur :

| URL | Notes |
|-----|-------|
| `https://localhost:8087` | Certificat valide (via mkcert) |
| `https://marketplace.qantis.local:8087` | Certificat valide + domaine métier |

---

## Résolution de problèmes

### ❌ Le navigateur affiche une erreur de certificat

Vérifier que `mkcert -install` a bien été exécuté **sur la machine courante**.  
Si vous avez mis à jour votre navigateur ou réinstallé macOS, relancez :

```bash
mkcert -install
```

### ❌ `make generate-certs` échoue avec "mkcert not found"

`mkcert` n'est pas installé. Suivre l'étape 1 ci-dessus.

### ❌ `https://marketplace.qantis.local:8087` ne répond pas

Vérifier que la ligne est bien présente dans `/etc/hosts` :

```bash
grep marketplace.qantis.local /etc/hosts
```

### ❌ Les certificats sont absents au démarrage du container

Le container nginx vérifie la présence des `.pem` au démarrage et **refuse de démarrer** si les fichiers sont absents, avec le message :

```
ERROR: SSL certificates missing in /etc/nginx/certs/. Run: make generate-certs
```

Générer les certificats puis relancer :

```bash
make generate-certs
make restart nginx
```

