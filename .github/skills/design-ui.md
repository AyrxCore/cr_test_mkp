# Skill: Designer l'UI

## Identité

Tu es un expert en design d'interface utilisateur avec TailwindCSS. Tu crées des interfaces visuellement cohérentes, accessibles et modernes.

## Design System

### Palette de Couleurs

```
Primary:    blue-600    (#2563EB)   - Actions principales
Secondary:  gray-600    (#4B5563)   - Actions secondaires
Success:    green-600   (#16A34A)   - Confirmations, succès
Warning:    yellow-500  (#EAB308)   - Alertes, attention
Danger:     red-600     (#DC2626)   - Erreurs, suppressions
Info:       sky-500     (#0EA5E9)   - Informations

Text:       gray-900    (#111827)   - Texte principal
Muted:      gray-500    (#6B7280)   - Texte secondaire
Background: gray-50     (#F9FAFB)   - Fond de page
Surface:    white       (#FFFFFF)   - Cartes, modales
```

### Espacements (Tailwind Scale)

```
Compact:    p-2, gap-2     (8px)
Default:    p-4, gap-4     (16px)
Relaxed:    p-6, gap-6     (24px)
Spacious:   p-8, gap-8     (32px)
```

## Composants de Base

### Boutons

```vue
<!-- Primary Button -->
<button class="px-4 py-2 bg-blue-600 text-white font-medium rounded-lg 
               hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2
               disabled:opacity-50 disabled:cursor-not-allowed
               transition-colors">
  Action principale
</button>

<!-- Secondary Button -->
<button class="px-4 py-2 bg-white text-gray-700 font-medium rounded-lg
               border border-gray-300 hover:bg-gray-50
               focus:ring-2 focus:ring-gray-500 focus:ring-offset-2
               transition-colors">
  Action secondaire
</button>

<!-- Danger Button -->
<button class="px-4 py-2 bg-red-600 text-white font-medium rounded-lg
               hover:bg-red-700 focus:ring-2 focus:ring-red-500 focus:ring-offset-2
               transition-colors">
  Supprimer
</button>

<!-- Icon Button -->
<button class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 
               rounded-lg transition-colors">
  <IconEdit class="w-5 h-5" />
</button>
```

### Cards

```vue
<!-- Basic Card -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
  <h3 class="text-lg font-semibold text-gray-900">Titre</h3>
  <p class="mt-2 text-gray-600">Description</p>
</div>

<!-- Clickable Card -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6
            hover:shadow-md hover:border-blue-300 
            cursor-pointer transition-all">
  <h3 class="text-lg font-semibold text-gray-900">Titre</h3>
</div>

<!-- Card avec Header -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
  <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
    <h3 class="font-semibold text-gray-900">Header</h3>
  </div>
  <div class="p-6">
    Contenu
  </div>
</div>
```

### Formulaires

```vue
<!-- Input -->
<div>
  <label class="block text-sm font-medium text-gray-700 mb-1">
    Email
  </label>
  <input 
    type="email"
    class="w-full px-3 py-2 border border-gray-300 rounded-lg
           focus:ring-2 focus:ring-blue-500 focus:border-blue-500
           placeholder:text-gray-400"
    placeholder="exemple@email.com"
  />
</div>

<!-- Input avec erreur -->
<div>
  <label class="block text-sm font-medium text-gray-700 mb-1">
    Email
  </label>
  <input 
    type="email"
    class="w-full px-3 py-2 border border-red-300 rounded-lg
           focus:ring-2 focus:ring-red-500 focus:border-red-500
           bg-red-50"
  />
  <p class="mt-1 text-sm text-red-600">Email invalide</p>
</div>

<!-- Select -->
<select class="w-full px-3 py-2 border border-gray-300 rounded-lg
               focus:ring-2 focus:ring-blue-500 focus:border-blue-500
               bg-white">
  <option value="">Sélectionner...</option>
  <option value="1">Option 1</option>
</select>

<!-- Checkbox -->
<label class="flex items-center gap-2 cursor-pointer">
  <input type="checkbox" 
         class="w-4 h-4 text-blue-600 border-gray-300 rounded
                focus:ring-blue-500" />
  <span class="text-sm text-gray-700">Accepter les conditions</span>
</label>
```

### Tables

```vue
<div class="overflow-x-auto">
  <table class="min-w-full divide-y divide-gray-200">
    <thead class="bg-gray-50">
      <tr>
        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
          Nom
        </th>
        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
          Email
        </th>
        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
          Actions
        </th>
      </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-200">
      <tr class="hover:bg-gray-50">
        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
          Jean Dupont
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
          jean@example.com
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
          <button class="text-blue-600 hover:text-blue-800">Modifier</button>
        </td>
      </tr>
    </tbody>
  </table>
</div>
```

### Badges & Status

```vue
<!-- Badge neutre -->
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
             bg-gray-100 text-gray-800">
  Draft
</span>

<!-- Badge success -->
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
             bg-green-100 text-green-800">
  Actif
</span>

<!-- Badge warning -->
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
             bg-yellow-100 text-yellow-800">
  En attente
</span>

<!-- Badge danger -->
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
             bg-red-100 text-red-800">
  Expiré
</span>
```

### Alertes

```vue
<!-- Info -->
<div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
  <p class="text-sm text-blue-800">Information importante</p>
</div>

<!-- Success -->
<div class="p-4 bg-green-50 border border-green-200 rounded-lg">
  <p class="text-sm text-green-800">Opération réussie</p>
</div>

<!-- Warning -->
<div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
  <p class="text-sm text-yellow-800">Attention requise</p>
</div>

<!-- Error -->
<div class="p-4 bg-red-50 border border-red-200 rounded-lg">
  <p class="text-sm text-red-800">Une erreur est survenue</p>
</div>
```

## Layouts

### Page avec Sidebar

```vue
<div class="flex h-screen bg-gray-100">
  <!-- Sidebar -->
  <aside class="w-64 bg-white border-r border-gray-200">
    <nav class="p-4 space-y-1">
      <!-- Nav items -->
    </nav>
  </aside>

  <!-- Main content -->
  <main class="flex-1 overflow-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Content -->
    </div>
  </main>
</div>
```

### Grid Responsive

```vue
<!-- 1 col mobile, 2 cols tablet, 3 cols desktop -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
  <Card />
  <Card />
  <Card />
</div>
```

## États UI

### Loading

```vue
<!-- Spinner -->
<div class="animate-spin h-5 w-5 border-2 border-blue-600 border-t-transparent rounded-full"></div>

<!-- Skeleton -->
<div class="animate-pulse space-y-4">
  <div class="h-4 bg-gray-200 rounded w-3/4"></div>
  <div class="h-4 bg-gray-200 rounded w-1/2"></div>
</div>

<!-- Button loading -->
<button class="px-4 py-2 bg-blue-600 text-white rounded-lg flex items-center gap-2" disabled>
  <span class="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full"></span>
  Chargement...
</button>
```

### Empty State

```vue
<div class="text-center py-12">
  <IconInbox class="mx-auto h-12 w-12 text-gray-400" />
  <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun adhérent</h3>
  <p class="mt-1 text-sm text-gray-500">Commencez par créer un adhérent.</p>
  <div class="mt-6">
    <button class="px-4 py-2 bg-blue-600 text-white rounded-lg">
      Créer un adhérent
    </button>
  </div>
</div>
```

## Accessibilité

```vue
<!-- Focus visible -->
<button class="focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">

<!-- Aria labels -->
<button aria-label="Fermer le modal">
  <IconX class="w-5 h-5" />
</button>

<!-- Role et état -->
<div role="alert" aria-live="polite">
  Message d'erreur
</div>
```

## Checklist UI

- [ ] Couleurs du design system respectées
- [ ] Espacements cohérents (scale Tailwind)
- [ ] États hover/focus/disabled présents
- [ ] Responsive (mobile-first)
- [ ] Loading states pour les actions async
- [ ] Empty states pour les listes vides
- [ ] Messages d'erreur visibles
- [ ] Accessibilité (aria, focus visible)

## Instructions

1. **TOUJOURS** utiliser TailwindCSS - JAMAIS de CSS custom
2. **TOUJOURS** inclure les états hover, focus, disabled
3. **TOUJOURS** penser mobile-first
4. **TOUJOURS** utiliser les couleurs du design system
5. **TOUJOURS** inclure les états de loading et empty
6. Privilégier la clarté et la simplicité

