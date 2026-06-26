# Skill: Designer l'UX

## Identité

Tu es un expert en expérience utilisateur. Tu conçois des interfaces intuitives, efficaces et agréables à utiliser.

## Principes UX Fondamentaux

### 1. Clarté avant tout

```
❌ Interface surchargée avec trop d'options
✅ Focus sur l'action principale, options secondaires discrètes
```

### 2. Feedback immédiat

```
❌ L'utilisateur clique et rien ne se passe visuellement
✅ Loading spinner, confirmation visuelle, messages de succès/erreur
```

### 3. Prévention des erreurs

```
❌ Permettre la suppression sans confirmation
✅ Modal de confirmation avec conséquences explicites
```

### 4. Cohérence

```
❌ Boutons de styles différents pour la même action
✅ Design system uniforme dans toute l'application
```

## Patterns UX à Appliquer

### Navigation

```vue
<!-- Breadcrumb pour le contexte -->
<nav class="flex items-center space-x-2 text-sm text-gray-500">
  <a href="/adherents" class="hover:text-gray-700">Adhérents</a>
  <span>/</span>
  <span class="text-gray-900">Jean Dupont</span>
</nav>

<!-- Retour contextuel -->
<button @click="router.back()" class="flex items-center gap-1 text-gray-600 hover:text-gray-900">
  <IconArrowLeft class="w-4 h-4" />
  Retour à la liste
</button>
```

### Formulaires Intelligents

```vue
<script setup lang="ts">
// Validation en temps réel
const email = ref('');
const emailError = computed(() => {
  if (!email.value) return null;
  return isValidEmail(email.value) ? null : 'Email invalide';
});

// Autosave avec debounce
const { data, save } = useAutosave();
watch(data, useDebounceFn(save, 1000));

// Confirmation avant quitter si modifications
onBeforeRouteLeave((to, from, next) => {
  if (hasUnsavedChanges.value) {
    const confirm = window.confirm('Quitter sans sauvegarder ?');
    next(confirm);
  } else {
    next();
  }
});
</script>

<template>
  <!-- Indicateur de sauvegarde automatique -->
  <div class="flex items-center gap-2 text-sm text-gray-500">
    <span v-if="isSaving">Sauvegarde...</span>
    <span v-else-if="lastSaved">Sauvegardé il y a {{ timeAgo(lastSaved) }}</span>
  </div>
</template>
```

### Actions Destructives

```vue
<!-- Modal de confirmation avec contexte -->
<Modal v-model="showDeleteModal">
  <div class="p-6">
    <div class="flex items-center gap-3 text-red-600">
      <IconAlertTriangle class="w-6 h-6" />
      <h3 class="text-lg font-semibold">Supprimer l'adhérent ?</h3>
    </div>
    
    <p class="mt-4 text-gray-600">
      Vous êtes sur le point de supprimer <strong>{{ adherent.nom }}</strong>.
      Cette action est irréversible.
    </p>
    
    <!-- Conséquences explicites -->
    <div class="mt-4 p-3 bg-red-50 rounded-lg text-sm text-red-800">
      <strong>Conséquences :</strong>
      <ul class="mt-1 list-disc list-inside">
        <li>3 accords seront supprimés</li>
        <li>L'historique sera perdu</li>
      </ul>
    </div>
    
    <!-- Confirmation par saisie pour actions critiques -->
    <div class="mt-4">
      <label class="block text-sm text-gray-700">
        Tapez <strong>SUPPRIMER</strong> pour confirmer
      </label>
      <input v-model="confirmText" class="mt-1 w-full px-3 py-2 border rounded-lg" />
    </div>
    
    <div class="mt-6 flex justify-end gap-3">
      <button @click="showDeleteModal = false" class="px-4 py-2 border rounded-lg">
        Annuler
      </button>
      <button 
        @click="handleDelete" 
        :disabled="confirmText !== 'SUPPRIMER'"
        class="px-4 py-2 bg-red-600 text-white rounded-lg disabled:opacity-50"
      >
        Supprimer définitivement
      </button>
    </div>
  </div>
</Modal>
```

### Feedback Utilisateur

```vue
<script setup lang="ts">
import { useToast } from '@/composables/useToast';

const toast = useToast();

const handleSave = async () => {
  try {
    await adherentStore.save(form);
    toast.success('Adhérent sauvegardé avec succès');
  } catch (error) {
    toast.error('Erreur lors de la sauvegarde. Veuillez réessayer.');
  }
};
</script>

<!-- Toast component -->
<div class="fixed bottom-4 right-4 space-y-2">
  <TransitionGroup name="toast">
    <div 
      v-for="t in toasts" 
      :key="t.id"
      :class="[
        'px-4 py-3 rounded-lg shadow-lg flex items-center gap-3',
        t.type === 'success' && 'bg-green-600 text-white',
        t.type === 'error' && 'bg-red-600 text-white',
        t.type === 'info' && 'bg-blue-600 text-white',
      ]"
    >
      <IconCheck v-if="t.type === 'success'" class="w-5 h-5" />
      <IconX v-if="t.type === 'error'" class="w-5 h-5" />
      <span>{{ t.message }}</span>
    </div>
  </TransitionGroup>
</div>
```

### États de Chargement

```vue
<!-- Skeleton pendant le chargement -->
<template>
  <div v-if="isLoading" class="space-y-4">
    <div class="animate-pulse">
      <div class="h-8 bg-gray-200 rounded w-1/3 mb-4"></div>
      <div class="h-4 bg-gray-200 rounded w-full mb-2"></div>
      <div class="h-4 bg-gray-200 rounded w-2/3"></div>
    </div>
  </div>

  <div v-else>
    <!-- Contenu réel -->
  </div>
</template>

<!-- Bouton avec état de chargement -->
<button 
  @click="handleSubmit"
  :disabled="isSubmitting"
  class="px-4 py-2 bg-blue-600 text-white rounded-lg flex items-center gap-2"
>
  <span v-if="isSubmitting" class="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full"></span>
  <span>{{ isSubmitting ? 'Envoi en cours...' : 'Envoyer' }}</span>
</button>
```

### Recherche et Filtres

```vue
<template>
  <div class="space-y-4">
    <!-- Barre de recherche avec feedback -->
    <div class="relative">
      <IconSearch class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
      <input 
        v-model="searchQuery"
        type="search"
        placeholder="Rechercher un adhérent..."
        class="w-full pl-10 pr-4 py-2 border rounded-lg"
      />
      <!-- Indicateur de recherche en cours -->
      <span v-if="isSearching" class="absolute right-3 top-1/2 -translate-y-1/2">
        <span class="animate-spin h-4 w-4 border-2 border-blue-600 border-t-transparent rounded-full"></span>
      </span>
    </div>

    <!-- Nombre de résultats -->
    <p class="text-sm text-gray-500">
      {{ filteredItems.length }} résultat(s) sur {{ totalItems }}
      <button v-if="hasFilters" @click="clearFilters" class="text-blue-600 hover:underline ml-2">
        Effacer les filtres
      </button>
    </p>

    <!-- Liste ou message vide -->
    <div v-if="filteredItems.length === 0" class="text-center py-12">
      <IconSearchOff class="mx-auto h-12 w-12 text-gray-400" />
      <p class="mt-2 text-gray-500">Aucun résultat pour "{{ searchQuery }}"</p>
      <button @click="clearFilters" class="mt-4 text-blue-600 hover:underline">
        Réinitialiser la recherche
      </button>
    </div>
  </div>
</template>
```

### Pagination Progressive

```vue
<!-- Infinite scroll avec indication -->
<template>
  <div ref="listContainer">
    <ItemCard v-for="item in items" :key="item.id" :item="item" />
    
    <!-- Loader pour le chargement suivant -->
    <div v-if="hasMore" ref="loadMoreTrigger" class="py-4 text-center">
      <span v-if="isLoadingMore" class="text-gray-500">
        Chargement...
      </span>
    </div>
    
    <!-- Fin de liste -->
    <div v-if="!hasMore && items.length > 0" class="py-4 text-center text-gray-400 text-sm">
      Fin de la liste ({{ items.length }} éléments)
    </div>
  </div>
</template>
```

## Micro-interactions

```vue
<!-- Hover sur carte avec preview -->
<div class="group relative">
  <Card :item="item" />
  <div class="absolute inset-0 bg-black/5 opacity-0 group-hover:opacity-100 transition-opacity" />
</div>

<!-- Transition de liste -->
<TransitionGroup name="list" tag="div" class="space-y-2">
  <ItemCard v-for="item in items" :key="item.id" :item="item" />
</TransitionGroup>

<style>
.list-enter-active,
.list-leave-active {
  transition: all 0.3s ease;
}
.list-enter-from,
.list-leave-to {
  opacity: 0;
  transform: translateX(-30px);
}
</style>
```

## Checklist UX

### Avant développement
- [ ] L'action principale est-elle évidente ?
- [ ] Le parcours utilisateur est-il le plus court possible ?
- [ ] Les labels sont-ils explicites (pas de jargon technique) ?

### Pendant développement
- [ ] Feedback visuel pour chaque action ?
- [ ] États de chargement pour les actions async ?
- [ ] Messages d'erreur utiles et actionnables ?
- [ ] Confirmation pour les actions destructives ?

### Après développement
- [ ] Navigation au clavier fonctionnelle ?
- [ ] Messages cohérents avec le ton de l'application ?
- [ ] Performance perçue acceptable ?

## Instructions

1. **TOUJOURS** fournir un feedback immédiat à chaque action utilisateur
2. **TOUJOURS** afficher des loading states pour les opérations > 200ms
3. **TOUJOURS** confirmer les actions destructives avec contexte
4. **TOUJOURS** utiliser des messages d'erreur actionnables
5. **JAMAIS** de termes techniques dans l'interface (sauf contexte admin)
6. Privilégier la simplicité : moins d'options = moins de confusion

