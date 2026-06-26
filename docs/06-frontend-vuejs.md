# 06 - Frontend Vue.js

## 🎨 Stack technique

| Technologie | Usage |
|-------------|-------|
| **Vue.js 3** | Framework frontend |
| **TypeScript** | Typage statique |
| **Composition API** | Pattern de développement (`<script setup>`) |
| **Pinia** | State management |
| **Vue Router** | Routing SPA |
| **Tailwind CSS** | Styling utilitaire |
| **Vite** | Bundler et dev server |
| **Axios** | Client HTTP |

## 📁 Structure des dossiers

```
assets/vuejs/
├── App.vue               # Composant racine
├── BaseTemplate.vue      # Layout principal avec header/footer
├── router.ts             # Configuration du router
├── store.ts              # Configuration Pinia
│
├── modules/              # Modules par fonctionnalité
│   ├── account/          # Mon compte
│   ├── actualites/       # Actualités/News
│   ├── cart/             # Panier et checkout
│   ├── contact/          # Contact
│   ├── home/             # Page d'accueil
│   ├── login/            # Authentification
│   ├── map/              # Carte des partenaires
│   ├── products/         # Catalogue produits
│   └── shared/           # Composants partagés
│
├── stores/               # Stores Pinia
│   ├── user.ts           # Utilisateur connecté
│   ├── cart.ts           # Panier
│   ├── channel.ts        # Channel actuel
│   ├── product.ts        # Produits
│   └── ...
│
├── services/             # Services utilitaires
│   ├── httpclient/       # Clients HTTP (Axios)
│   │   ├── BaseClientService.ts
│   │   ├── UserHttpClient.ts
│   │   ├── CartHttpClient.ts
│   │   ├── ProductHttpClient.ts
│   │   └── ...
│   ├── formatter.ts      # Formatage (prix, dates...)
│   ├── utils.ts          # Fonctions utilitaires
│   └── gtm.ts            # Google Tag Manager
│
├── types/                # Interfaces TypeScript
│   ├── User.ts
│   ├── Cart.ts
│   ├── Product.ts
│   └── ...
│
├── constants/            # Constantes
├── directives/           # Directives Vue custom
└── router/               # Configuration des routes
    └── pages-list.ts     # Liste des pages
```

## 🧩 Anatomie d'un module

Chaque module suit cette structure :

```
modules/products/
├── components/           # Composants spécifiques au module
│   ├── ProductCard.vue
│   ├── ProductFilters.vue
│   └── ProductGrid.vue
├── composables/          # Logique réutilisable (hooks)
│   └── useProductSearch.ts
├── views/                # Pages (vues)
│   ├── ProductListPage.vue
│   └── ProductDetailPage.vue
├── utils/                # Utilitaires du module
├── routerProducts.ts     # Routes du module
└── index.ts              # Exports
```

## 🏪 Stores Pinia

### Exemple : User Store

```typescript
// stores/user.ts
import { defineStore } from 'pinia'

export const useUserStore = defineStore('user', {
  state: (): UserStoreState => ({
    user: null,
    isNeoAutoLogin: getCookie('neoAutoLogin') === 'true',
    userLocation: null,
  }),

  actions: {
    // Authentification
    async authenticate(userData: AuthenticateUserData): Promise<Account[]> {
      const accounts = await UserHttpClient.get().getUserToken(userData)
      return accounts
    },
    
    // Sélectionner un compte
    async selectUserAccount(id: string): Promise<boolean> {
      await UserHttpClient.get().selectUserAccount(id)
      return true
    },
    
    // Récupérer les infos user
    async getCurrentUserData(): Promise<void> {
      this.user = await UserHttpClient.get().getUserMe()
    },
  },

  getters: {
    isAuthenticated: (state) => state.user !== null,
    currentAccount: (state) => state.user?.account,
  },
})
```

### Stores principaux

| Store | Description |
|-------|-------------|
| `useUserStore` | Utilisateur connecté, authentification |
| `useCartStore` | Panier, ajout/suppression produits |
| `useChannelStore` | Channel actuel (design, options) |
| `useProductStore` | Produits, recherche, filtres |
| `useCategoryStore` | Catégories du catalogue |
| `useOrderStore` | Commandes |
| `useFavoriteStore` | Listes de favoris |
| `useAlertStore` | Notifications/alertes UI |

## 🌐 Services HTTP

### BaseClientService

Classe de base avec configuration Axios :

```typescript
// services/BaseClientService.ts
class BaseClientService {
  public apiClient: AxiosInstance

  constructor(isPatch: boolean) {
    this.apiClient = axios.create({
      baseURL: '/api',
      withCredentials: true,
      headers: {
        'Content-Type': isPatch 
          ? 'application/merge-patch+json' 
          : 'application/json',
      },
    })

    // Intercepteur : ajoute le header X-channel
    this.apiClient.interceptors.request.use((config) => {
      config.headers['X-channel'] = getCommonStore().channelCode
      return config
    })

    // Intercepteur : gère les 401 (redirect vers login)
    this.apiClient.interceptors.response.use(
      (response) => response,
      (error) => {
        if (error.response?.status === 401) {
          Cookies.remove('BEARER')
          location.reload()
        }
        return Promise.reject(error)
      }
    )
  }

  // Singleton pattern
  static get<T extends typeof BaseClientService>(isPatch = false): InstanceType<T> {
    // ...
  }
}
```

### Exemple : ProductHttpClient

```typescript
// services/httpclient/ProductHttpClient.ts
export default class ProductHttpClient extends BaseClientService {
  public getProduct<T extends Product>(id: number): Promise<T> {
    return this.apiClient
      .get<T>(`buyer/product/${id}`)
      .then((response) => response.data)
  }

  public searchProducts<T extends SearchResult>(filters: ProductFilters): Promise<T> {
    return this.apiClient
      .post<T>('buyer/search/product', filters)
      .then((response) => response.data)
  }
}
```

## 🛣️ Routing

### Configuration principale

```typescript
// router.ts
const routes: RouteRecordRaw[] = [
  { path: '/', redirect: { name: PageList.HOME_PAGE } },
  { path: '/home', name: PageList.HOME_PAGE, component: Home },
  { path: '/contact', name: PageList.CONTACT_PAGE, component: Contact },
  
  // Import des routes de modules
  ...productsRoutes,
  ...cartRoutes,
  ...accountRoutes,
  ...actualitesRoutes,
  
  // 404
  { path: '/:pathMatch(.*)*', name: PageList.NOT_FOUND, component: NotFoundPage },
]

const router = createRouter({
  history: createWebHistory('/app'),
  routes,
})
```

### Routes d'un module

```typescript
// modules/products/routerProducts.ts
export const routes: RouteRecordRaw[] = [
  {
    path: '/products',
    name: ProductPageList.PRODUCT_LIST,
    component: () => import('./views/ProductListPage.vue'),
  },
  {
    path: '/products/:id/:slug?',
    name: ProductPageList.PRODUCT_DETAIL,
    component: () => import('./views/ProductDetailPage.vue'),
    props: true,
  },
]
```

## 🎨 Composants

### Convention de nommage

- **PascalCase** pour les noms de fichiers : `ProductCard.vue`
- **kebab-case** dans les templates : `<product-card />`

### Structure d'un composant

```vue
<!-- components/ProductCard.vue -->
<script setup lang="ts">
import { computed } from 'vue'
import type { Product } from '@/vuejs/types/Product'

// Props typées
interface Props {
  product: Product
  showPrice?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  showPrice: true,
})

// Emits typés
const emit = defineEmits<{
  (e: 'add-to-cart', productId: number): void
}>()

// Computed
const formattedPrice = computed(() => {
  return new Intl.NumberFormat('fr-FR', {
    style: 'currency',
    currency: 'EUR',
  }).format(props.product.price)
})

// Methods
const handleAddToCart = () => {
  emit('add-to-cart', props.product.id)
}
</script>

<template>
  <div class="product-card bg-white rounded-lg shadow p-4">
    <img :src="product.image" :alt="product.name" class="w-full h-48 object-cover" />
    <h3 class="text-lg font-semibold mt-2">{{ product.name }}</h3>
    <p v-if="showPrice" class="text-primary font-bold">{{ formattedPrice }}</p>
    <button 
      @click="handleAddToCart"
      class="mt-4 bg-primary text-white px-4 py-2 rounded hover:bg-primary-dark"
    >
      Ajouter au panier
    </button>
  </div>
</template>
```

## 📦 Types TypeScript

```typescript
// types/Product.ts
export interface Product {
  id: number
  name: string
  slug: string
  description: string
  price: number
  priceReference?: number
  images: ProductImage[]
  variants: ProductVariant[]
  company: Company
  properties: ProductProperty[]
}

export interface ProductVariant {
  id: number
  sku: string
  price: number
  stock: number
}

export interface ProductImage {
  id: number
  url: string
  position: number
}
```

## 🔧 Commandes de développement

```bash
# Depuis le container Node
npm run dev          # Dev server avec hot-reload
npm run build        # Build production
npm run lint         # Linting ESLint
npm run type-check   # Vérification TypeScript
```

