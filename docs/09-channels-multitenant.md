# 09 - Multi-tenant (Channels)

## 🏢 Concept

Le projet supporte le **multi-tenant** via le système de **Channels**. Chaque channel représente une marque/client différente avec :

- Son propre **design** (logo, couleurs, favicon)
- Ses propres **documents légaux**
- Ses propres **adhérents**
- Ses propres **options/features**

```
┌────────────────────────────────────────────────────────────┐
│                       Channels                              │
├──────────────┬──────────────┬──────────────┬───────────────┤
│ QANTIS_ACHAT │    CEDAP     │   OPTEAM     │    FSPF       │
│              │              │              │               │
│ marketplace  │ achats.cedap │ opteamachats │ privileges.   │
│ .qantis.co   │ .asso.fr     │ .fr          │ fspf.fr       │
└──────────────┴──────────────┴──────────────┴───────────────┘
```

## ⚙️ Configuration des Channels

### Fichier de configuration

```yaml
# config/channels.yaml
parameters:
  channels:
    - name: QANTIS Marketplace
      code: QANTIS_ACHAT
      hostname: marketplace.qantis.co
      channelParameter:
        email: marketplace@qantis.co
        phoneNumber: '+33437650621'
        logo: https://s3.../logo-qantis.png
        favicon: https://s3.../favicon-qantis.svg
        primaryColor: '#050056'
        secondaryColor: '#404FE6'
        textColor: '#6b7280'
        privacyPolicy: '/politique-de-confidentialite'
        legalTerms: '/mentions-legales'
        generalTermsOfUse: '/conditions-generales-d-utilisation'
        whiteLabel: false
      channelOptions:
        - { name: 'BANNER_HOMEPAGE', value: null }
        - { name: 'FAVORITES', value: null }
        - { name: 'PRE_HOME_IMAGE', value: 'https://s3.../img.png' }
        # ...

    - name: Cedap Service Achats
      code: CEDAP
      hostname: achats.cedap.asso.fr
      channelParameter:
        email: serviceachats@cedap.asso.fr
        logo: https://s3.../logo-cedap.png
        primaryColor: '#a10d59'
        whiteLabel: true  # Marque blanche
      # ...
```

### Options de Channel

```yaml
# config/channel_option_keys.yaml
parameters:
  channel_option_keys:
    - BANNER_HOMEPAGE           # Bannière page d'accueil
    - BANNER_TITLE_HOMEPAGE     # Titre de la bannière
    - BANNER_FLASH_HOMEPAGE     # Bannière flash
    - BANNER_SLIDER_HOMEPAGE    # Slider
    - RSE_HOMEPAGE              # Section RSE
    - SUPPLIER_PARTNERS_HOMEPAGE # Partenaires fournisseurs
    - EXPERT_CONTENT_HOMEPAGE   # Contenu expert
    - FAVORITES                 # Fonctionnalité favoris
    - PRE_HOME_IMAGE            # Image pré-home
    - PRE_HOME_TITLE            # Titre pré-home
    - PRE_HOME_SUBTITLE         # Sous-titre pré-home
    - PRE_HOME_LIST             # Liste de points pré-home
    # ...
```

## 🔄 Flux de détection du Channel

### Côté Frontend

```typescript
// stores/channel.ts
export const useChannelStore = defineStore('channel', {
  actions: {
    async getChannel(hostname: string): Promise<void> {
      // Appel API pour récupérer le channel par hostname
      const channel = await ChannelHttpClient.get().getChannelByHost(hostname)
      
      this.currentChannel = {
        id: channel.id,
        code: channel.code,
        name: channel.name,
        design: {
          primaryColor: channel.design.primaryColor,
          secondaryColor: channel.design.secondaryColor,
          logo: channel.design.logo,
          // ...
        },
        options: channel.options,
      }
    },
  },
})
```

### Header X-channel

Toutes les requêtes API incluent le header `X-channel` :

```typescript
// services/BaseClientService.ts
this.apiClient.interceptors.request.use((config) => {
  if (!config.url.includes('/channels/by-host/')) {
    const store = getCommonStore()
    config.headers['X-channel'] = store.channelCode
  }
  return config
})
```

### Côté Backend

```php
// src/Context/ChannelContext.php
class ChannelContext
{
    public function getChannel(): ?Channel
    {
        $request = $this->requestStack->getCurrentRequest();
        $channelCode = $request->headers->get('X-channel');
        
        return $this->channelRepository->findOneBy(['code' => $channelCode]);
    }
}
```

## 🎨 Personnalisation du design

### Variables CSS dynamiques

```vue
<!-- App.vue -->
<script setup>
const channelStore = useChannelStore()

// Applique les couleurs du channel au :root CSS
watch(() => channelStore.channel, (channel) => {
  if (channel) {
    document.documentElement.style.setProperty('--color-primary', channel.design.primaryColor)
    document.documentElement.style.setProperty('--color-secondary', channel.design.secondaryColor)
    document.documentElement.style.setProperty('--color-text', channel.design.textColor)
  }
})
</script>
```

### Tailwind avec couleurs dynamiques

```js
// tailwind.config.js
module.exports = {
  theme: {
    extend: {
      colors: {
        primary: 'var(--color-primary)',
        secondary: 'var(--color-secondary)',
        'text-main': 'var(--color-text)',
      },
    },
  },
}
```

### Utilisation dans les composants

```vue
<template>
  <button class="bg-primary text-white hover:bg-secondary">
    Action
  </button>
</template>
```

## 🔐 Feature Flags (Options)

### Vérification côté Frontend

```typescript
// stores/channel.ts
getters: {
  hasFeature: (state) => (featureName: string): boolean => {
    return state.currentChannel?.options?.[featureName] === 'true'
  },
}

// Utilisation
const channelStore = useChannelStore()

if (channelStore.hasFeature('FAVORITES')) {
  // Afficher la fonctionnalité favoris
}
```

### Composant conditionnel

```vue
<template>
  <div v-if="channelStore.isAllowedToShow('BANNER_HOMEPAGE')">
    <HomeBanner />
  </div>
  
  <div v-if="channelStore.isAllowedToShow('FAVORITES')">
    <FavoritesList />
  </div>
</template>
```

## 🏗️ Entités liées

### Channel

```php
class Channel
{
    private ?Uuid $id;
    private ?string $name;           // "QANTIS Marketplace"
    private ?string $code;           // "QANTIS_ACHAT" (unique)
    private ?string $hostname;       // "marketplace.qantis.co"
    private ?ChannelParameter $channelParameter;
    private Collection $channelOptions;
    private Collection $adherents;
}
```

### ChannelParameter

```php
class ChannelParameter
{
    private ?string $email;
    private ?string $phoneNumber;
    private ?string $logo;
    private ?string $favicon;
    private ?string $primaryColor;
    private ?string $secondaryColor;
    private ?string $textColor;
    private ?string $privacyPolicy;
    private ?string $legalTerms;
    private ?string $generalTermsOfUse;
    private bool $whiteLabel = false;
}
```

### ChannelOption

```php
class ChannelOption
{
    private ?Channel $channel;
    private ?string $name;   // "BANNER_HOMEPAGE"
    private ?string $value;  // URL de l'image ou null
}
```

## 📝 Ajouter un nouveau Channel

1. **Ajouter la config** dans `config/channels.yaml` :

```yaml
- name: Mon Nouveau Channel
  code: MON_CHANNEL
  hostname: mon-channel.example.com
  channelParameter:
    email: contact@mon-channel.com
    logo: https://...
    primaryColor: '#123456'
    # ...
```

2. **Lancer les migrations/seeds** :

```bash
make database-migrations
# Ou commande spécifique de seed des channels
```

3. **Configurer le DNS** pour pointer vers l'application

4. **Tester** en accédant à `https://mon-channel.example.com`

## 🔍 Bonnes pratiques

1. **Toujours vérifier le channel** avant d'accéder aux données sensibles
2. **Utiliser les feature flags** pour activer/désactiver des fonctionnalités
3. **Ne pas hardcoder** les couleurs ou logos
4. **Tester chaque channel** lors des déploiements
5. **Centraliser** la logique de détection du channel

